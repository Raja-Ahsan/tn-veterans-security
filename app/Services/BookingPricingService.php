<?php

namespace App\Services;

use App\Models\Service;

class BookingPricingService
{
    /**
     * @return array{
     *     baseTotal: float,
     *     travelFees: float,
     *     totalAmount: float,
     *     depositAmount: float,
     *     remainingAmount: float
     * }
     */
    public function calculate(Service $service, int $numStudents): array
    {
        $numStudents = max(1, $numStudents);
        $baseTotal = (float) ($service->price ?? 0) * $numStudents;
        $travelFees = $service->is_travel_based ? $this->getTravelFeesTotal($service) : 0.0;
        $totalAmount = $baseTotal + $travelFees;
        $depositAmount = $service->getResolvedDepositAmount() * $numStudents;
        $remainingAmount = max(0, $totalAmount - $depositAmount);

        return [
            'baseTotal' => $baseTotal,
            'travelFees' => $travelFees,
            'totalAmount' => $totalAmount,
            'depositAmount' => $depositAmount,
            'remainingAmount' => $remainingAmount,
        ];
    }

    public function getTravelFeesTotal(Service $service): float
    {
        if (! $service->is_travel_based) {
            return 0.0;
        }

        return (float) ($service->travel_distance_fee ?? 0)
            + (float) ($service->travel_lodging_fee ?? 0)
            + (float) ($service->travel_time_fee ?? 0);
    }

    public function meetsTravelMinimum(Service $service, int $numStudents): bool
    {
        if (! $service->is_travel_based || ! $service->travel_minimum_students) {
            return true;
        }

        return $numStudents >= (int) $service->travel_minimum_students;
    }
}
