<?php

namespace GetawayFinder\Tests;

use GetawayFinder\Models\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    public function testGetNearbyHotelsReturnsArray()
    {
        $hotels = Search::getNearbyHotels(123.456, -78.901);
        
        $this->assertIsArray($hotels);
    }

    public function testFetchApiResponsesReturnsArrayOfResponses()
    {
        $search = new Search();
        $responses = $search->fetchApiResponses();
        
        $this->assertIsArray($responses);
        $this->assertNotEmpty($responses);
    }
}
