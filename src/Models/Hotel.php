<?php

namespace GetawayFinder\Models;

class Hotel
{
    public string $name;
    public float $latitude;
    public float $longitude;
    public float $pricePerNight;
    public float $distance;

    /**
     * Creates a new instance of the class.
     *
     * @param string $name The name of the instance.
     * @param float $latitude The latitude of the instance.
     * @param float $longitude The longitude of the instance.
     * @param float $pricePerNight The price per night of the instance.
     */
    public function __construct($name, $latitude, $longitude, $pricePerNight)
    {
        $this->name = $name;
        $this->latitude = floatval($latitude);
        $this->longitude = floatval($longitude);
        $this->pricePerNight = floatval($pricePerNight);
    }


    /**
     * Calculates the distance between two coordinates.
     *
     * @param float $lat1 The latitude of the first coordinate.
     * @param float $lon1 The longitude of the first coordinate.
     * @return float The distance between the two coordinates.
     */
    public function calculateDistance(float $lat1, float $lon1) :float
    {
        $lat2 = $this->latitude;
        $lon2 = $this->longitude;
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dlat = deg2rad($lat2 - $lat1);
        $dlon = deg2rad($lon2 - $lon1);

        $a = sin($dlat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dlon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $this->distance = $earthRadius * $c;
        return $this->distance;
    }
}
