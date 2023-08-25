<?php

namespace GetawayFinder\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use InvalidArgumentException;

use GetawayFinder\Components\Cache;

class Search
{

    const ORDER_PROXIMITY = 'proximity';
    const ORDER_PRICE_PER_NIGHT = 'pricepernight';
    const ORDER_BY_LIST = [
        self::ORDER_PROXIMITY => 'Proximity', 
        self::ORDER_PRICE_PER_NIGHT => 'Price per Night'
    ];

    public $httpClient;
    private $cache;

    /**
     * Constructs a new instance of the class.
     *
     * @return void
     */
    public function __construct()
    {
        $this->httpClient = new Client();
        $this->cache = new Cache();
    }

    /**
     * Sets the HTTP client for the object.
     *
     * @param mixed $httpClient The HTTP client to set.
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Retrieves nearby hotels based on the given latitude and longitude.
     *
     * @param float $latitude The latitude of the location.
     * @param float $longitude The longitude of the location.
     * @param string $orderBy The order in which the hotels should be sorted. Default is 'proximity'.
     * @return array The array of formatted results representing nearby hotels.
     */
    public static function getNearbyHotels(float $latitude, float $longitude, string $orderBy = 'proximity'): array
    {
        $search = new self();
        $apiResponses = $search->fetchApiResponses();

        $hotels = $search->processApiResponses($apiResponses, $latitude, $longitude);

        $formattedResults = $search->formatResults($hotels, $orderBy);

        return $formattedResults;
    }

    /**
     * Fetches API responses from a list of URLs and returns an array.
     *
     * @return array The API responses.
     */
    public function fetchApiResponses(): array
    {
        $cachedResponses = $this->cache->get('api_responses');
        if(!empty($cachedResponses)) {
            return $cachedResponses;
        }

        $apiUrls = [
            "https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_1.json",
            "https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json",
            // Add more URLs if necessary
        ];

        $promises = array_map(fn($url) => $this->httpClient->getAsync($url), $apiUrls);
        $responses = Promise\Utils::unwrap($promises);
        $responses = array_map(fn($response) => json_decode($response->getBody(), true), $responses);
        
        $this->cache->set('api_responses', $responses, '10 minutes');
        return $responses;
    }

    /**
     * Process API responses and return an array of hotels.
     *
     * @param array $responses The array of API responses.
     * @param float $latitude The latitude of the location.
     * @param float $longitude The longitude of the location.
     * @return array The array of Hotel objects.
     */
    public function processApiResponses(array $responses, float $latitude, float $longitude): array
    {
        $hotels = [];
        foreach ($responses as $response) {
            if (isset($response['success']) && $response['success']) {
                foreach ($response['message'] as $hotelData) {
                    $hotel = new Hotel(...$hotelData);
                    $hotel->calculateDistance($latitude, $longitude);
                    $hotels[] = $hotel;
                }
            }
        }

        return $hotels;
    }

    /**
     * Formats an array of hotels based on the specified ordering.
     *
     * @param array $hotels The array of hotels to be formatted.
     * @param string $orderBy The ordering criteria for the hotels.
     *                       Valid values are 'proximity' and 'pricepernight'.
     * @throws InvalidArgumentException If an invalid orderBy value is provided.
     * @return array The formatted array of hotels.
     */
    public function formatResults(array $hotels, string $orderBy): array
    {
        
        if ($orderBy === self::ORDER_PRICE_PER_NIGHT) {
            usort($hotels, fn($a, $b) => $a->pricePerNight <=> $b->pricePerNight);
        } else {
            usort($hotels, fn($a, $b) => $a->distance <=> $b->distance);
        }
        
        return array_map(fn($hotel) => sprintf(
            "Hotel %s, %.2f KM, %.2f EUR",
            $hotel->name,
            $hotel->distance,
            $hotel->pricePerNight
        ), $hotels);
    }

}
