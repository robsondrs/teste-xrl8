<?php

use GetawayFinder\Models\Hotel;
use PHPUnit\Framework\TestCase;

class HotelTest extends TestCase
{
    public function testCalculateDistance()
    {
        $hotel = new Hotel('Hotel A', 40.7128, -74.0060, 100);

        // Calculate distance from a specific point (e.g., New York City)
        $distance = $hotel->calculateDistance(40.7128, -74.0060);
        
        $this->assertEquals(0.0, $distance); // Distance to the same point should be 0

        // Calculate distance from a different point
        $distance = round($hotel->calculateDistance(34.0522, -118.2437), 2);

        // Provide an acceptable delta due to floating-point precision
        $this->assertEquals(3935.75, $distance, '', 0.01);
    }
}
