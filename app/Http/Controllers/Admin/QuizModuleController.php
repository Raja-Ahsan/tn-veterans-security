<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizModuleController extends Controller
{
    public function index(Request $request): View
    {
        $delivery = $request->string('delivery')->toString();
        if (! Service::isValidDeliveryFormat($delivery)) {
            $delivery = Service::DELIVERY_BLENDED;
        }

        $deliveryCounts = Service::deliveryCountMap();

        $services = Service::query()
            ->exactDelivery($delivery)
            ->withCount('courseModules')
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        return view('admin.quiz-modules.index', compact('services', 'delivery', 'deliveryCounts'));
    }
}
