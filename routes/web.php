<?php

use App\Http\Controllers\ClassCalendarController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\QuickBooksController;
use App\Http\Controllers\ServicePageController;
use App\Models\ClassSchedule;
use App\Support\PublicTrainingServiceQuery;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/*
| Laravel's "auth" middleware and exception handler fall back to route("login").
| This app uses admin.login and student.login instead; register "login" here.
*/
Route::get('/route-clear', function () {
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    $cache = 'Route cache cleared <br /> View cache cleared <br /> Cache cleared <br /> Config cleared <br /> Config cache cleared';

    return $cache;
});

Route::get('/login', function (\Illuminate\Http\Request $request) {
    $intended = (string) $request->session()->get('url.intended', '');

    if ($intended !== '' && str_contains($intended, '/student/')) {
        return redirect()->route('student.login');
    }

    return redirect()->route('admin.login');
})->name('login');

Route::get('/', function () {
    // Get services (grouped structure: each service can appear in multiple categories)
    $allServices = \App\Models\Service::where('is_active', true)
        ->orderBy('order')
        ->get();
    $servicesByCategory = collect();
    foreach ($allServices as $service) {
        $cats = $service->categories ?? [];
        foreach ($cats as $cat) {
            if (! $servicesByCategory->has($cat)) {
                $servicesByCategory->put($cat, collect());
            }
            $servicesByCategory->get($cat)->push($service);
        }
    }

    // Also get services for the "Explore Training Programs" section (limit 6)
    $featuredServices = \App\Models\Service::where('is_active', true)
        ->orderBy('order')
        ->limit(6)
        ->get();

    return view('welcome', compact('servicesByCategory', 'featuredServices', 'allServices'));
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/privacy-policy', function () {
    return view('legal.privacy-policy');
})->name('legal.privacy-policy');

Route::get('/terms-and-conditions', function () {
    return view('legal.terms-and-conditions');
})->name('legal.terms-and-conditions');

Route::get('/all-services', function () {
    $allServices = PublicTrainingServiceQuery::apply(
        \App\Models\Service::where('is_active', true)
    )
        ->orderBy('order')
        ->get();

    return view('all-services', compact('allServices'));
})->name('all-services');

Route::get('/class-calendar', [ClassCalendarController::class, 'index'])->name('class-calendar');

Route::get('/training-services', function () {
    $category = request()->query('category');
    $subcategory = request()->query('subcategory');

    $query = PublicTrainingServiceQuery::apply(
        \App\Models\Service::where('is_active', true)
    );

    if ($category) {
        $query->whereJsonContains('categories', $category);
    }

    if ($subcategory) {
        $query->where('subcategory', $subcategory);
    }

    $services = $query->orderBy('order')->orderBy('created_at', 'desc')->get();

    // Get all unique categories from services (excluding security training & renewals)
    $categories = PublicTrainingServiceQuery::apply(
        \App\Models\Service::where('is_active', true)
    )
        ->get()
        ->pluck('categories')
        ->flatten()
        ->filter()
        ->unique()
        ->values();

    return view('services', compact('services', 'categories', 'category', 'subcategory'));
})->name('services');

Route::get('/affiliated-services', function () {
    return view('affiliated-services');
})->name('affiliated-services');

Route::get('/nra-services', function () {
    return view('nra-services');
})->name('nra-services');
// Services by Page

Route::get('/training-services/enhanced-armed-guard-security-subcategories', function () {
    $rifleService = \App\Models\Service::where('is_active', true)->find(34);
    $shotgunService = \App\Models\Service::where('is_active', true)->find(35);
    $services = collect([$rifleService, $shotgunService])->filter();

    return view('enhanced-armed-guard-subcategories', compact('services'));
})->name('handgun.subcategories');

Route::get('/training-services/{id}', [ServicePageController::class, 'showById'])->name('service.details');
Route::get('/service/{slug}', [ServicePageController::class, 'showBySlug'])->name('service.by.slug')->where('slug', '[a-z0-9\-]+');

Route::post('/training-services/{service}/booking-inquiry', function (\App\Models\Service $service, \Illuminate\Http\Request $request) {
    $bookableCount = ClassSchedule::where('service_id', $service->id)
        ->where('status', 'scheduled')
        ->where('class_date', '>=', now()->toDateString())
        ->whereRaw('current_students < max_students')
        ->count();

    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'nullable|string|max:50',
        'number_of_students' => 'nullable|integer|min:1|max:100',
        'location' => 'nullable|string|max:255',
    ];

    if ($bookableCount > 0) {
        $rules['class_schedule_id'] = [
            'required',
            'integer',
            Rule::exists('class_schedules', 'id')->where(function ($q) use ($service) {
                $q->where('service_id', $service->id)
                    ->where('status', 'scheduled')
                    ->where('class_date', '>=', now()->toDateString())
                    ->whereRaw('current_students < max_students');
            }),
        ];
    } else {
        $rules['class_schedule_id'] = 'nullable|integer';
    }

    $validated = $request->validate($rules);

    $numStudents = max(1, (int) ($request->input('number_of_students', 1)));

    if (! empty($validated['class_schedule_id'])) {
        $sched = ClassSchedule::where('id', $validated['class_schedule_id'])
            ->where('service_id', $service->id)
            ->firstOrFail();

        if ($numStudents > $sched->getAvailableSpots()) {
            throw ValidationException::withMessages([
                'number_of_students' => ['Only '.$sched->getAvailableSpots().' seat(s) available for this session.'],
            ]);
        }

        if (($service->class_type ?? 'group') === 'group' && $numStudents < $sched->min_students) {
            throw ValidationException::withMessages([
                'number_of_students' => ['This session requires at least '.$sched->min_students.' student(s).'],
            ]);
        }

        $loc = $request->input('location');
        if ($loc !== null && $loc !== '' && $loc !== 'Any location') {
            $schedLoc = $sched->location ?: 'No Specific Location';
            if ($schedLoc !== $loc) {
                throw ValidationException::withMessages([
                    'class_schedule_id' => ['Pick a session that matches your location filter, or choose “Any location”.'],
                ]);
            }
        }
    }

    $validated['number_of_students'] = $numStudents;
    session()->put('booking_inquiry_'.$service->id, $validated);

    // Create student account if guest, so they don't need to sign up separately
    $wasNewStudent = false;
    if (! \Illuminate\Support\Facades\Auth::guard('student')->check()) {
        $student = \App\Models\Student::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)),
            ]
        );
        if ($student->wasRecentlyCreated) {
            $wasNewStudent = true;
            \Illuminate\Support\Facades\Auth::guard('student')->login($student);
            $request->session()->regenerate();
        } else {
            // Update name/phone for existing student
            $student->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? $student->phone,
            ]);
        }
    }

    $message = $wasNewStudent
        ? 'Account created. Review your booking and proceed to payment.'
        : 'Review your booking and complete payment.';

    return redirect()->route('student.services.checkout', $service->id)
        ->with('success', $message);
})->name('service.booking.inquiry')->middleware('throttle:10,1');

Route::get('/security-training', function () {
    return view('security-training');
})->name('security-training');

// Security Training: Initial Security – dynamic services (category = security_training; exclude renewals so no double)
Route::get('/intial-security', function () {
    $services = \App\Models\Service::where('is_active', true)
        ->whereJsonContains('categories', 'security_training')
        ->orderBy('order')
        ->orderBy('created_at', 'desc')
        ->get()
        ->filter(fn ($s) => ! in_array('renewals', $s->categories ?? []))
        ->values();

    return view('intial-security', compact('services'));
})->name('intial-security');

// Security Training: Renewals – dynamic services (category = renewals), card click → service detail form
Route::get('/renewals', function () {
    $services = \App\Models\Service::where('is_active', true)
        ->whereJsonContains('categories', 'renewals')
        ->orderBy('order')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('renewals', compact('services'));
})->name('renewals');

// Dallas Law training page (canonical URL; same content as /service/dallas-law)
Route::get('/dallas-law', fn () => app(ServicePageController::class)->showBySlug('dallas-law'))->name('dallas-law');

Route::get('/testimonials', function () {
    return view('testimonials');
})->name('testimonials');

Route::get('/contact-us', function () {
    $captchaA = random_int(1, 9);
    $captchaB = random_int(1, 9);
    session(['contact_captcha_answer' => $captchaA + $captchaB]);

    return view('contact', compact('captchaA', 'captchaB'));
})->name('contact');

Route::post('/contact-us', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/private-protective-services', function () {
    // Get services in the "services" category (Private Protective Services)
    $services = \App\Models\Service::where('is_active', true)
        ->whereJsonContains('categories', 'services')
        ->orderBy('order')
        ->get();

    // Get security company links from database
    $companyLinks = \App\Models\SecurityCompanyLink::where('is_active', true)
        ->orderBy('order')
        ->get();

    return view('private-protective-services', compact('services', 'companyLinks'));
})->name('private-protective-services');

// Legacy /customer/* URLs → /student/* (permanent redirects)
Route::permanentRedirect('/customer/{path?}', '/student/{path?}')->where('path', '.*');

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    // Auth Routes (Public)
    Route::get('/login', [App\Http\Controllers\Student\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Student\AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [App\Http\Controllers\Student\AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Student\AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/logout', [App\Http\Controllers\Student\AuthController::class, 'logout'])->name('logout');

    // Public Routes - View available classes (no login required)
    Route::get('/services/{serviceId}/available-classes', [App\Http\Controllers\Student\BookingController::class, 'showAvailableClasses'])->name('available-classes');

    // Checkout (public – shows summary; login required to complete payment)
    Route::get('/services/{serviceId}/checkout', [App\Http\Controllers\Student\BookingController::class, 'showCheckout'])->name('services.checkout');

    // Protected Student Routes
    Route::middleware([\App\Http\Middleware\AuthenticateStudent::class])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile');
        Route::post('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/payment-history', [App\Http\Controllers\Student\PaymentHistoryController::class, 'index'])->name('payment-history');
        Route::get('/online-courses', [App\Http\Controllers\Student\OnlineCoursesController::class, 'index'])->name('online-courses.index');
        Route::get('/certificates', [App\Http\Controllers\Student\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [App\Http\Controllers\Student\CertificateController::class, 'show'])->name('certificates.show');
        Route::post('/waitlist/{classSchedule}', [App\Http\Controllers\Student\WaitlistController::class, 'store'])->name('waitlist.store');

        Route::get('/courses/{service}/online', [App\Http\Controllers\Student\OnlineCourseController::class, 'index'])->name('online-course.index');
        Route::get('/courses/{service}/online/modules/{courseModule}', [App\Http\Controllers\Student\OnlineCourseController::class, 'show'])->name('online-course.module');
        Route::post('/courses/{service}/online/modules/{courseModule}/quiz', [App\Http\Controllers\Student\OnlineCourseController::class, 'submitQuiz'])->name('online-course.quiz');
        Route::post('/courses/{service}/online/modules/{courseModule}/quiz/start', [App\Http\Controllers\Student\OnlineCourseController::class, 'startQuiz'])->name('online-course.quiz.start');
        Route::get('/courses/{service}/online/modules/{courseModule}/quiz/take', [App\Http\Controllers\Student\OnlineCourseController::class, 'takeQuiz'])->name('online-course.quiz.take');
        Route::post('/courses/{service}/online/modules/{courseModule}/quiz/answer', [App\Http\Controllers\Student\OnlineCourseController::class, 'answerQuiz'])->name('online-course.quiz.answer');
        Route::get('/courses/{service}/online/modules/{courseModule}/quiz/result/{moduleQuizSession}', [App\Http\Controllers\Student\OnlineCourseController::class, 'quizResult'])->name('online-course.quiz.result');

        // Booking Routes
        Route::get('/bookings', [App\Http\Controllers\Student\BookingController::class, 'index'])->name('bookings');
        Route::get('/bookings/{id}', [App\Http\Controllers\Student\BookingController::class, 'show'])->name('bookings.show');
        Route::get('/services/{serviceId}/book', [App\Http\Controllers\Student\BookingController::class, 'create'])->name('booking.create');
        Route::get('/services/{serviceId}/book/{scheduleId}', [App\Http\Controllers\Student\BookingController::class, 'create'])->name('booking.create.schedule');
        Route::post('/bookings', [App\Http\Controllers\Student\BookingController::class, 'store'])->name('booking.store');

        // Checkout – create booking from inquiry and go to payment

        Route::post('/services/{serviceId}/checkout', [App\Http\Controllers\Student\BookingController::class, 'processCheckout'])->name('services.checkout.process');

        // Payment Routes
        Route::get('/bookings/{bookingId}/payment', [App\Http\Controllers\Student\BookingController::class, 'showPayment'])->name('booking.payment');
        Route::post('/bookings/{bookingId}/payment', [App\Http\Controllers\Student\BookingController::class, 'processPayment'])->name('booking.payment.process');
        Route::get('/bookings/{bookingId}/payment/quickbooks-session', [App\Http\Controllers\Student\BookingController::class, 'getQuickBooksPaymentSession'])->name('booking.payment.quickbooks.session');
        Route::post('/bookings/{bookingId}/payment/quickbooks', [App\Http\Controllers\Student\BookingController::class, 'processQuickBooksPayment'])->name('booking.payment.quickbooks');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/services', '/admin/classes', 301);
    Route::redirect('/services/create', '/admin/classes/create', 301);
    Route::get('/services/{service}/{path?}', function (string $service, ?string $path = null) {
        $target = '/admin/classes/'.$service;

        if ($path) {
            $target .= '/'.$path;
        }

        return redirect($target, 301);
    })->where('path', '.*');

    // Auth Routes
    Route::get('/login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/students', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/edit', [App\Http\Controllers\Admin\StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'update'])->name('students.update');

        Route::get('/contact-submissions', [App\Http\Controllers\Admin\ContactSubmissionController::class, 'index'])->name('contact-submissions.index');
        Route::get('/contact-submissions/{contactSubmission}', [App\Http\Controllers\Admin\ContactSubmissionController::class, 'show'])->name('contact-submissions.show');
        Route::put('/contact-submissions/{contactSubmission}/status', [App\Http\Controllers\Admin\ContactSubmissionController::class, 'updateStatus'])->name('contact-submissions.update-status');

        Route::resource('instructors', App\Http\Controllers\Admin\InstructorController::class)->except(['show']);
        Route::resource('locations', App\Http\Controllers\Admin\LocationController::class)->except(['show']);

        Route::get('/communication-logs', [App\Http\Controllers\Admin\CommunicationLogController::class, 'index'])->name('communication-logs.index');
        Route::get('/communication-logs/{communicationLog}', [App\Http\Controllers\Admin\CommunicationLogController::class, 'show'])->name('communication-logs.show');
        Route::post('/class-schedules/{classSchedule}/notify', [App\Http\Controllers\Admin\ClassNotificationController::class, 'store'])->name('class-schedules.notify');
        Route::post('/class-schedules/{classSchedule}/notify-waitlist', [App\Http\Controllers\Admin\ClassNotificationController::class, 'notifyWaitlist'])->name('class-schedules.notify-waitlist');

        Route::resource('classes', App\Http\Controllers\Admin\ServiceController::class)
            ->names('classes')
            ->parameters(['classes' => 'service']);
        Route::get('/classes/{service}/course-modules', [App\Http\Controllers\Admin\CourseModuleController::class, 'index'])->name('classes.course-modules.index');
        Route::get('/classes/{service}/course-modules/create', [App\Http\Controllers\Admin\CourseModuleController::class, 'create'])->name('classes.course-modules.create');
        Route::post('/classes/{service}/course-modules', [App\Http\Controllers\Admin\CourseModuleController::class, 'store'])->name('classes.course-modules.store');
        Route::get('/classes/{service}/course-modules/{courseModule}/edit', [App\Http\Controllers\Admin\CourseModuleController::class, 'edit'])->name('classes.course-modules.edit');
        Route::put('/classes/{service}/course-modules/{courseModule}', [App\Http\Controllers\Admin\CourseModuleController::class, 'update'])->name('classes.course-modules.update');
        Route::delete('/classes/{service}/course-modules/{courseModule}', [App\Http\Controllers\Admin\CourseModuleController::class, 'destroy'])->name('classes.course-modules.destroy');
        Route::post('/classes/{service}/course-modules/reorder', [App\Http\Controllers\Admin\CourseModuleController::class, 'reorder'])->name('classes.course-modules.reorder');
        Route::get('/classes/{service}/blended-progress', [App\Http\Controllers\Admin\BlendedCourseAdminController::class, 'studentProgress'])->name('classes.blended-progress');
        Route::post('/classes/{service}/blended-progress/{student}/modules/{courseModule}/override', [App\Http\Controllers\Admin\BlendedCourseAdminController::class, 'overrideModule'])->name('classes.blended-progress.override');
        Route::post('/classes/{service}/blended-progress/{student}/modules/{courseModule}/reset', [App\Http\Controllers\Admin\BlendedCourseAdminController::class, 'resetModule'])->name('classes.blended-progress.reset');
        Route::post('/classes/{service}/blended-progress/{student}/in-person-test', [App\Http\Controllers\Admin\BlendedCourseAdminController::class, 'storeInPersonTest'])->name('classes.blended-progress.in-person-test');
        Route::get('/classes/{service}/blended-progress/{student}/modules/{courseModule}/attempts/{attempt}', [App\Http\Controllers\Admin\BlendedCourseAdminController::class, 'attemptReview'])->name('classes.blended-progress.attempt');
        Route::get('/certificates', [App\Http\Controllers\Admin\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'show'])->name('certificates.show');
        Route::get('/certificates/{certificate}/print', [App\Http\Controllers\Admin\CertificateController::class, 'print'])->name('certificates.print');
        Route::delete('/certificates/{certificate}', [App\Http\Controllers\Admin\CertificateController::class, 'destroy'])->name('certificates.destroy');
        Route::resource('class-schedules', App\Http\Controllers\Admin\ClassScheduleController::class)->names('class-schedules');
        Route::post('/class-schedules/{classSchedule}/travel-notify', [App\Http\Controllers\Admin\TravelClassController::class, 'notify'])->name('class-schedules.travel-notify');
        Route::post('/class-schedules/{classSchedule}/travel-cancel', [App\Http\Controllers\Admin\TravelClassController::class, 'cancel'])->name('class-schedules.travel-cancel');

        // Bookings Routes
        Route::get('/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');
        Route::get('/class-schedules/{classSchedule}/roster/export', [App\Http\Controllers\Admin\BookingController::class, 'exportRoster'])->name('class-schedules.roster.export');
        Route::put('/bookings/{booking}/status', [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::post('/bookings/{booking}/mark-deposit-paid', [App\Http\Controllers\Admin\BookingController::class, 'markDepositPaid'])->name('bookings.mark-deposit-paid');

        // Payments Routes
        Route::get('/payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments-from-quickbooks', [App\Http\Controllers\Admin\PaymentController::class, 'quickbooksPayments'])->name('payments.quickbooks-list');
        Route::get('/payments/{payment}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/sync-quickbooks', [App\Http\Controllers\Admin\PaymentController::class, 'syncQuickBooks'])->name('payments.sync-quickbooks');
        Route::post('/payments/{payment}/sync-bank', [App\Http\Controllers\Admin\PaymentController::class, 'syncBank'])->name('payments.sync-bank');
        Route::post('/payments/sync-all-quickbooks', [App\Http\Controllers\Admin\PaymentController::class, 'syncAllQuickBooks'])->name('payments.sync-all-quickbooks');
        Route::post('/payments/sync-all-bank', [App\Http\Controllers\Admin\PaymentController::class, 'syncAllBank'])->name('payments.sync-all-bank');

        Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

        // Security Company Links
        Route::resource('security-company-links', App\Http\Controllers\Admin\SecurityCompanyLinkController::class);

        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::post('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::get('/admin/quickbooks/connect', [QuickBooksController::class, 'connect'])
    ->name('quickbooks.connect');

Route::get('/admin/quickbooks/callback', [QuickBooksController::class, 'callback'])
    ->name('quickbooks.callback');
