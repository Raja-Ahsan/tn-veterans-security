<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Services\BookingPricingService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingPricingServiceTest extends TestCase
{
    #[Test]
    public function it_calculates_travel_fees_and_per_student_deposit(): void
    {
        $service = new Service([
            'price' => 100,
            'deposit_amount' => 25,
            'is_travel_based' => true,
            'travel_distance_fee' => 50,
            'travel_lodging_fee' => 30,
            'travel_time_fee' => 20,
            'travel_minimum_students' => 4,
        ]);

        $pricing = (new BookingPricingService)->calculate($service, 2);

        $this->assertSame(200.0, $pricing['baseTotal']);
        $this->assertSame(100.0, $pricing['travelFees']);
        $this->assertSame(300.0, $pricing['totalAmount']);
        $this->assertSame(50.0, $pricing['depositAmount']);
        $this->assertSame(250.0, $pricing['remainingAmount']);
    }

    #[Test]
    public function it_enforces_travel_minimum_students(): void
    {
        $service = new Service([
            'is_travel_based' => true,
            'travel_minimum_students' => 4,
        ]);

        $pricing = new BookingPricingService;

        $this->assertFalse($pricing->meetsTravelMinimum($service, 2));
        $this->assertTrue($pricing->meetsTravelMinimum($service, 4));
    }
}
