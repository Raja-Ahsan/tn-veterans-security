<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewBookingAlertMail;
use App\Mail\AdminPaymentReceivedAlertMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingPendingPaymentMail;
use App\Models\ClassSchedule;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Services\AdminNotifier;
use App\Services\BookingPricingService;
use App\Services\EnrollmentConfirmationService;
use App\Services\QuickBooksPaymentsService;
use App\Services\StudentNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function __construct(
        private EnrollmentConfirmationService $enrollmentConfirmation,
        private BookingPricingService $pricing,
    ) {}

    /**
     * Show student's bookings list.
     */
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();
        $filter = $request->query('filter', 'all');

        $query = ServiceBooking::where('student_id', $student->id)
            ->with(['service', 'classSchedule']);

        if ($filter === 'upcoming') {
            $query->whereIn('status', ['pending', 'confirmed'])
                ->where('booking_date', '>=', now()->toDateString());
        } elseif ($filter === 'past') {
            $query->where(function ($q) {
                $q->whereIn('status', ['completed', 'cancelled'])
                    ->orWhere('booking_date', '<', now()->toDateString());
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('student.bookings', compact('bookings', 'filter'));
    }

    /**
     * Show available classes for a service.
     */
    public function showAvailableClasses($serviceId)
    {
        $service = Service::where('is_active', true)->findOrFail($serviceId);

        // Get available class schedules
        $schedules = ClassSchedule::where('service_id', $service->id)
            ->where('status', 'scheduled')
            ->where('class_date', '>=', now()->toDateString())
            ->whereRaw('current_students < max_students')
            ->with(['locationRecord', 'instructorRecord'])
            ->orderBy('class_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $fullSchedules = ClassSchedule::where('service_id', $service->id)
            ->where('class_date', '>=', now()->toDateString())
            ->where(function ($q) {
                $q->where('status', 'full')
                    ->orWhereRaw('current_students >= max_students');
            })
            ->with(['locationRecord', 'instructorRecord'])
            ->orderBy('class_date', 'asc')
            ->get();

        return view('student.available-classes', compact('service', 'schedules', 'fullSchedules'));
    }

    /**
     * Show checkout: booking summary and payment step (from service-details inquiry).
     */
    public function showCheckout($serviceId)
    {
        $service = Service::where('is_active', true)->findOrFail($serviceId);
        $inquiry = session('booking_inquiry_'.$service->id);
        if (! $inquiry) {
            return redirect()->route('training-classes.show', $service->id)
                ->with('error', 'Please complete the booking form first.');
        }
        $numStudents = (int) ($inquiry['number_of_students'] ?? 1);
        $pricing = $this->pricing->calculate($service, $numStudents);
        $totalAmount = $pricing['totalAmount'];
        $depositAmount = $pricing['depositAmount'];
        $travelFees = $pricing['travelFees'];
        $baseTotal = $pricing['baseTotal'];
        $amountDue = $depositAmount;
        $isLoggedIn = Auth::guard('student')->check();
        if (! $isLoggedIn) {
            session()->put('url.intended', route('student.classes.checkout', $service->id));
        }

        $selectedSchedule = null;
        if (! empty($inquiry['class_schedule_id'])) {
            $selectedSchedule = ClassSchedule::where('service_id', $service->id)
                ->where('id', $inquiry['class_schedule_id'])
                ->first();
        }

        return view('student.checkout', compact(
            'service',
            'inquiry',
            'amountDue',
            'totalAmount',
            'depositAmount',
            'numStudents',
            'isLoggedIn',
            'selectedSchedule',
            'travelFees',
            'baseTotal'
        ));
    }

    /**
     * Process checkout: create booking from inquiry and redirect to payment.
     */
    public function processCheckout(Request $request, $serviceId)
    {
        $request->validate([
            'policy_acknowledged' => 'accepted',
        ]);

        $student = Auth::guard('student')->user();
        $service = Service::where('is_active', true)->findOrFail($serviceId);
        $inquiry = session('booking_inquiry_'.$service->id);
        if (! $inquiry) {
            return redirect()->route('training-classes.show', $service->id)
                ->with('error', 'Session expired. Please complete the booking form again.');
        }
        $numStudents = max(1, (int) ($inquiry['number_of_students'] ?? 1));
        $preferredLocation = $inquiry['location'] ?? null;

        if (! $this->pricing->meetsTravelMinimum($service, $numStudents)) {
            return redirect()->route('student.classes.checkout', $service->id)
                ->with('error', "This travel class requires at least {$service->travel_minimum_students} student(s).");
        }

        $pricing = $this->pricing->calculate($service, $numStudents);
        $totalAmount = $pricing['totalAmount'];
        $depositAmount = $pricing['depositAmount'];
        $remainingAmount = $pricing['remainingAmount'];

        $scheduleId = $inquiry['class_schedule_id'] ?? null;

        DB::beginTransaction();
        try {
            $schedule = null;
            if ($scheduleId) {
                $schedule = ClassSchedule::where('service_id', $service->id)
                    ->where('id', $scheduleId)
                    ->lockForUpdate()
                    ->first();

                if (! $schedule || $schedule->status !== 'scheduled') {
                    throw new \RuntimeException('This class session is no longer available.');
                }

                if ($numStudents > $schedule->getAvailableSpots()) {
                    throw new \RuntimeException('Not enough seats left for this session.');
                }

                if (($service->class_type ?? 'group') === 'group' && $numStudents < $schedule->min_students) {
                    throw new \RuntimeException("This session requires at least {$schedule->min_students} student(s).");
                }
            }

            $bookingDate = $schedule
                ? $schedule->class_date->toDateString()
                : now()->toDateString();
            $bookingTime = $schedule
                ? Carbon::parse($schedule->start_time)->format('H:i:s')
                : null;

            $location = $schedule && $schedule->location
                ? $schedule->location
                : (($preferredLocation && $preferredLocation !== 'Any location' && $preferredLocation !== 'No Specific Location')
                    ? $preferredLocation
                    : null);

            $booking = ServiceBooking::create([
                'student_id' => $student->id,
                'service_id' => $service->id,
                'class_schedule_id' => $schedule?->id,
                'location' => $location,
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime,
                'status' => 'pending',
                'booking_type' => $service->class_type ?? 'group',
                'number_of_students' => $numStudents,
                'group_name' => $inquiry['name'] ?? null,
                'notes' => null,
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => 'pending',
            ]);
            DB::commit();

            $this->sendBookingPendingPaymentEmail($booking);
            $this->sendAdminNewBookingEmail($booking);

            return redirect()->route('student.booking.payment', $booking->id)
                ->with('success', 'Please complete payment for your booking.');
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return redirect()->route('student.classes.checkout', $service->id)
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('student.classes.checkout', $service->id)
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show booking form for a specific class schedule.
     */
    public function create($serviceId, $scheduleId = null)
    {
        $service = Service::where('is_active', true)->findOrFail($serviceId);
        $student = Auth::guard('student')->user();

        $schedule = null;
        if ($scheduleId) {
            $schedule = ClassSchedule::where('service_id', $service->id)
                ->where('id', $scheduleId)
                ->firstOrFail();

            // Check if schedule has available spots
            if (! $schedule->hasAvailableSpots()) {
                return redirect()->route('student.available-classes', $service->id)
                    ->with('error', 'This class is full. Please select another schedule.');
            }
        }

        // Get available schedules if no specific schedule selected
        if (! $schedule) {
            $schedules = ClassSchedule::where('service_id', $service->id)
                ->where('status', 'scheduled')
                ->where('class_date', '>=', now()->toDateString())
                ->whereRaw('current_students < max_students')
                ->orderBy('class_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
        } else {
            $schedules = collect([$schedule]);
        }

        return view('student.create-booking', compact('service', 'schedules', 'schedule', 'student'));
    }

    /**
     * Store a new booking.
     */
    // public function store(Request $request)
    // {
    //     $student = Auth::guard('student')->user();

    //     $validated = $request->validate([
    //         'service_id' => 'required|exists:services,id',
    //         'class_schedule_id' => 'required|exists:class_schedules,id',
    //         'number_of_students' => 'required|integer|min:1|max:10',
    //         'group_name' => 'nullable|string|max:255',
    //         'notes' => 'nullable|string|max:1000',
    //     ]);

    //     // Get service and schedule
    //     $service = Service::findOrFail($validated['service_id']);
    //     $schedule = ClassSchedule::findOrFail($validated['class_schedule_id']);

    //     // Validate capacity
    //     $availableSpots = $schedule->getAvailableSpots();
    //     if ($validated['number_of_students'] > $availableSpots) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', "Only {$availableSpots} spot(s) available. Please adjust the number of students.");
    //     }

    //     // Validate minimum students if it's a group booking
    //     if ($service->class_type === 'group' && $validated['number_of_students'] < $schedule->min_students) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', "Minimum {$schedule->min_students} student(s) required for this class.");
    //     }

    //     // Calculate amounts (fixed $20 deposit)
    //     $totalAmount = $service->price * $validated['number_of_students'];
    //     $depositAmount = 20;
    //     $remainingAmount = $totalAmount - $depositAmount;

    //     // Create booking in transaction
    //     DB::beginTransaction();
    //     try {
    //         // Create booking
    //         $booking = ServiceBooking::create([
    //             'student_id' => $student->id,
    //             'service_id' => $service->id,
    //             'class_schedule_id' => $schedule->id,
    //             'location' => $schedule->location, // Store location from schedule
    //             'booking_date' => $schedule->class_date,
    //             'booking_time' => Carbon::parse($schedule->start_time)->format('H:i:s'),
    //             'status' => 'pending',
    //             'booking_type' => $service->class_type,
    //             'number_of_students' => $validated['number_of_students'],
    //             'group_name' => $validated['group_name'] ?? null,
    //             'notes' => $validated['notes'] ?? null,
    //             'total_amount' => $totalAmount,
    //             'deposit_amount' => $depositAmount,
    //             'remaining_amount' => $remainingAmount,
    //             'payment_status' => 'pending',
    //         ]);

    //         // Update schedule student count
    //         $schedule->increment('current_students', $validated['number_of_students']);

    //         // Check if class is now full
    //         if ($schedule->current_students >= $schedule->max_students) {
    //             $schedule->update(['status' => 'full']);
    //         }

    //         DB::commit();

    //         // Redirect to payment page
    //         return redirect()->route('student.booking.payment', $booking->id)
    //             ->with('success', 'Booking created successfully. Please complete your deposit payment.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'An error occurred. Please try again.');
    //     }
    // }

    public function store(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'number_of_students' => 'required|integer|min:1|max:100',
            'group_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::where('is_active', true)->findOrFail($validated['service_id']);

        DB::beginTransaction();
        try {
            $schedule = ClassSchedule::where('id', $validated['class_schedule_id'])
                ->where('service_id', $service->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $schedule->hasAvailableSpots()) {
                throw new \RuntimeException('This class is full. Please pick another session.');
            }

            $numStudents = (int) $validated['number_of_students'];
            if ($numStudents > $schedule->getAvailableSpots()) {
                throw new \RuntimeException('Only '.$schedule->getAvailableSpots().' seat(s) available.');
            }

            if (($service->class_type ?? 'group') === 'group' && $numStudents < $schedule->min_students) {
                throw new \RuntimeException("Minimum {$schedule->min_students} student(s) required for this class.");
            }

            $pricing = $this->pricing->calculate($service, $numStudents);
            $totalAmount = $pricing['totalAmount'];
            $depositAmount = $pricing['depositAmount'];
            $remainingAmount = $pricing['remainingAmount'];

            if (! $this->pricing->meetsTravelMinimum($service, $numStudents)) {
                throw new \RuntimeException("This travel class requires at least {$service->travel_minimum_students} student(s).");
            }

            $booking = ServiceBooking::create([
                'student_id' => $student->id,
                'service_id' => $service->id,
                'class_schedule_id' => $schedule->id,
                'location' => $schedule->location,
                'booking_date' => $schedule->class_date->toDateString(),
                'booking_time' => Carbon::parse($schedule->start_time)->format('H:i:s'),
                'status' => 'pending',
                'booking_type' => $service->class_type ?? 'group',
                'number_of_students' => $numStudents,
                'group_name' => $validated['group_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => 'pending',
            ]);

            DB::commit();

            $this->sendBookingPendingPaymentEmail($booking);
            $this->sendAdminNewBookingEmail($booking);

            return redirect()->route('student.booking.payment', $booking->id)
                ->with('success', 'Please complete your deposit payment.');
        } catch (\RuntimeException $e) {
            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show deposit payment page.
     */
    public function showPayment($bookingId)
    {
        $student = Auth::guard('student')->user();
        $booking = ServiceBooking::where('student_id', $student->id)
            ->with(['service', 'classSchedule'])
            ->findOrFail($bookingId);

        // Check if deposit already paid
        if ($booking->payment_status === 'deposit_paid' || $booking->payment_status === 'fully_paid') {
            return redirect()->route('student.bookings.show', $booking->id)
                ->with('info', 'Deposit has already been paid.');
        }

        $qbPaymentsService = app(QuickBooksPaymentsService::class);
        $qbConnection = $qbPaymentsService->connectionState();
        $qbPaymentsEnabled = $qbConnection['ready'];
        $qbPaymentsMessage = $qbConnection['message'];
        $qbEnv = optional(\App\Models\SiteSetting::first())->quickbooks_environment ?? 'sandbox';
        $allowManualDeposit = ! $qbPaymentsEnabled;

        return view('student.booking-payment', compact(
            'booking',
            'qbPaymentsEnabled',
            'qbPaymentsMessage',
            'qbEnv',
            'allowManualDeposit'
        ));
    }

    /**
     * Get QuickBooks Payments session token for client-side tokenization.
     */
    public function getQuickBooksPaymentSession($bookingId)
    {
        $student = Auth::guard('student')->user();
        $booking = ServiceBooking::where('student_id', $student->id)->findOrFail($bookingId);
        if ($booking->payment_status === 'deposit_paid' || $booking->payment_status === 'fully_paid') {
            return response()->json(['error' => 'Deposit already paid.'], 400);
        }
        $qbPayments = app(QuickBooksPaymentsService::class);
        if (! $qbPayments->isEnabled()) {
            return response()->json(['error' => 'QuickBooks Payments not configured.'], 400);
        }
        try {
            $token = $qbPayments->getAccessToken();

            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process deposit payment via QuickBooks Payments (card).
     */
    public function processQuickBooksPayment(Request $request, $bookingId)
    {
        $student = Auth::guard('student')->user();
        $booking = ServiceBooking::where('student_id', $student->id)
            ->findOrFail($bookingId);

        if ($booking->payment_status === 'deposit_paid' || $booking->payment_status === 'fully_paid') {
            return redirect()->route('student.bookings.show', $booking->id)
                ->with('info', 'Deposit has already been paid.');
        }

        $validated = $request->validate([
            'card_number' => 'required|string|min:13|max:19',
            'exp_month' => 'required|string|min:1|max:2',
            'exp_year' => 'required|string|min:2|max:4',
            'cvc' => 'required|string|min:3|max:4',
        ]);

        $qbPayments = app(QuickBooksPaymentsService::class);
        if (! $qbPayments->isEnabled()) {
            return redirect()->route('student.booking.payment', $booking->id)
                ->with('error', 'QuickBooks Payments is not configured.');
        }

        $chargeAmount = (float) $booking->deposit_amount;
        $result = $qbPayments->createChargeFromCard(
            $validated['card_number'],
            $validated['exp_month'],
            $validated['exp_year'],
            $validated['cvc'],
            $chargeAmount
        );

        if (! $result['success']) {
            $message = $result['message'] ?? 'Please try again.';
            if (
                str_contains($message, 'invalid_grant')
                || str_contains(strtolower($message), 'token invalid')
                || str_contains(strtolower($message), 'token expired')
            ) {
                $message = 'Card payments are temporarily unavailable. An administrator must reconnect QuickBooks in Site Settings, or mark your deposit as paid from Bookings.';
            }

            return redirect()->route('student.booking.payment', $booking->id)
                ->with('error', 'Payment failed: '.$message);
        }

        $isFullPayment = $chargeAmount >= $booking->total_amount;
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'student_id' => $student->id,
            'amount' => $chargeAmount,
            'payment_type' => $isFullPayment ? 'full_payment' : 'deposit',
            'payment_method' => 'credit_card',
            'transaction_id' => $result['charge_id'],
            'payment_gateway' => 'quickbooks_payments',
            'gateway_response' => ['charge_id' => $result['charge_id']],
            'status' => 'completed',
            'payment_date' => now(),
        ]);

        // $booking->update([
        //     'payment_status' => $isFullPayment ? 'fully_paid' : 'deposit_paid',
        //     'status' => 'confirmed',
        // ]);

        DB::beginTransaction();

        try {
            if ($booking->class_schedule_id) {
                $schedule = ClassSchedule::lockForUpdate()->findOrFail($booking->class_schedule_id);
                if ($booking->number_of_students > $schedule->getAvailableSpots()) {
                    throw new \Exception('Seats no longer available for this session.');
                }
                $schedule->incrementStudentCount($booking->number_of_students);
            } else {
                $service = Service::lockForUpdate()->findOrFail($booking->service_id);
                $availableSpots = $service->max_students - $service->current_students;
                if ($booking->number_of_students > $availableSpots) {
                    throw new \Exception('Seats no longer available.');
                }
                $service->increment('current_students', $booking->number_of_students);
            }

            $booking->update([
                'payment_status' => $isFullPayment ? 'fully_paid' : 'deposit_paid',
                'status' => 'confirmed',
            ]);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('student.booking.payment', $booking->id)
                ->with('error', $e->getMessage());
        }
        $this->enrollmentConfirmation->sendAfterSuccessfulDeposit($booking, $payment);
        $this->sendAdminPaymentReceivedEmail($booking, $payment);

        $redirect = redirect()->route('student.bookings.show', $booking->id)
            ->with('success', 'Deposit payment received. Your booking is confirmed!');

        // if ($quickBooksSyncError !== null) {
        //     $redirect->with('warning', 'QuickBooks sync failed: ' . $quickBooksSyncError . ' You can retry from Admin → Payments → Sync to QuickBooks.');
        // }
        return $redirect;
    }

    /**
     * Process deposit payment (manual fallback when QuickBooks is unavailable).
     */
    public function processPayment(Request $request, $bookingId)
    {
        $student = Auth::guard('student')->user();
        $booking = ServiceBooking::where('student_id', $student->id)
            ->findOrFail($bookingId);

        if (in_array($booking->payment_status, ['deposit_paid', 'fully_paid'], true)) {
            return redirect()->route('student.bookings.show', $booking->id)
                ->with('info', 'Deposit has already been paid.');
        }

        $qbReady = app(QuickBooksPaymentsService::class)->isReady();
        if ($qbReady) {
            return redirect()->route('student.booking.payment', $booking->id)
                ->with('error', 'Please use the card payment form.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:manual',
        ]);

        $chargeAmount = (float) $booking->deposit_amount;
        $payment = null;

        DB::beginTransaction();
        try {
            if ($booking->class_schedule_id) {
                $schedule = ClassSchedule::lockForUpdate()->findOrFail($booking->class_schedule_id);
                if ($booking->number_of_students > $schedule->getAvailableSpots()) {
                    throw new \RuntimeException('Seats no longer available for this session.');
                }
                $schedule->incrementStudentCount($booking->number_of_students);
            } else {
                $svc = Service::lockForUpdate()->findOrFail($booking->service_id);
                $availableSpots = $svc->max_students - $svc->current_students;
                if ($booking->number_of_students > $availableSpots) {
                    throw new \RuntimeException('Seats no longer available.');
                }
                $svc->increment('current_students', $booking->number_of_students);
            }

            $isFullPayment = $chargeAmount >= (float) $booking->total_amount;
            $booking->update([
                'payment_status' => $isFullPayment ? 'fully_paid' : 'deposit_paid',
                'status' => 'confirmed',
                'remaining_amount' => max(0, (float) $booking->total_amount - $chargeAmount),
            ]);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'student_id' => $student->id,
                'amount' => $chargeAmount,
                'payment_type' => $isFullPayment ? 'full_payment' : 'deposit',
                'payment_method' => 'other',
                'transaction_id' => 'MANUAL-DEP-'.$booking->id.'-'.now()->format('YmdHis'),
                'payment_gateway' => 'manual',
                'notes' => 'Deposit completed via manual fallback (QuickBooks unavailable).',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('student.booking.payment', $booking->id)
                ->with('error', $e->getMessage());
        }

        $this->enrollmentConfirmation->sendAfterSuccessfulDeposit($booking->fresh(['student', 'service', 'classSchedule']), $payment);
        $this->sendAdminPaymentReceivedEmail($booking, $payment);

        return redirect()->route('student.bookings.show', $booking->id)
            ->with('success', 'Deposit recorded. Your booking is confirmed and online modules are unlocked.');
    }

    /**
     * Show booking details.
     */
    public function show($id)
    {
        $student = Auth::guard('student')->user();
        $booking = ServiceBooking::where('student_id', $student->id)
            ->with(['service', 'classSchedule', 'payments'])
            ->findOrFail($id);

        return view('student.booking-details', compact('booking'));
    }

    public function paymentHistory()
    {
        $student = Auth::guard('student')->user();

        $payments = Payment::where('student_id', $student->id)
            ->with(['booking.service'])
            ->orderByDesc('payment_date')
            ->paginate(15);

        return view('student.payment-history', compact('payments'));
    }

    private function sendBookingPendingPaymentEmail(ServiceBooking $booking): void
    {
        try {
            $booking->loadMissing(['student', 'service', 'classSchedule']);
            Mail::to($booking->student->email)->send(new BookingPendingPaymentMail($booking));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send booking pending payment email', [
                'booking_id' => $booking->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($booking->student) {
            $classTitle = $booking->service?->title ?? 'your class';
            StudentNotifier::push(
                $booking->student,
                'Booking created — payment pending',
                "Complete payment to confirm your seat in {$classTitle}.",
                'credit-card',
                route('student.booking.payment', $booking->id),
                'booking'
            );

            AdminNotifier::broadcast(
                'New booking — payment pending',
                "{$booking->student->name} booked {$classTitle}. Payment is still pending.",
                'calendar',
                route('admin.bookings.show', $booking),
                'booking'
            );
        }
    }

    private function sendBookingConfirmedEmail(ServiceBooking $booking, Payment $payment): void
    {
        try {
            $booking->loadMissing(['student', 'service', 'classSchedule']);
            Mail::to($booking->student->email)->send(new BookingConfirmedMail($booking, $payment));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send booking confirmed email', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($booking->student) {
            $classTitle = $booking->service?->title ?? 'your class';
            StudentNotifier::push(
                $booking->student,
                'Booking confirmed',
                "Payment received. You are confirmed for {$classTitle}.",
                'check',
                route('student.bookings.show', $booking->id),
                'booking'
            );

            AdminNotifier::broadcast(
                'Booking payment received',
                "{$booking->student->name} paid and is confirmed for {$classTitle}.",
                'credit-card',
                route('admin.bookings.show', $booking),
                'payment'
            );
        }
    }

    private function sendAdminNewBookingEmail(ServiceBooking $booking): void
    {
        $this->sendAdminEmail(new AdminNewBookingAlertMail($booking), $booking, null, 'new booking');
    }

    private function sendAdminPaymentReceivedEmail(ServiceBooking $booking, Payment $payment): void
    {
        $this->sendAdminEmail(new AdminPaymentReceivedAlertMail($booking, $payment), $booking, $payment, 'payment received');
    }

    private function sendAdminEmail(
        mixed $mailable,
        ServiceBooking $booking,
        ?Payment $payment,
        string $context
    ): void {
        try {
            $booking->loadMissing(['student', 'service', 'classSchedule']);
            $adminEmails = User::query()
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($adminEmails === []) {
                return;
            }

            Mail::to($adminEmails)->send($mailable);
        } catch (\Throwable $exception) {
            Log::warning('Failed to send admin notification email', [
                'context' => $context,
                'booking_id' => $booking->id,
                'payment_id' => $payment?->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
