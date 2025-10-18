<?php

namespace Tests\Unit;

use App\Helpers\TunisiaCities;
use PHPUnit\Framework\TestCase;

class TunisiaCitiesTest extends TestCase
{
    public function test_get_cities_returns_expected_keys_and_values(): void
    {
        $cities = TunisiaCities::getCities();

        $this->assertIsArray($cities);
        $this->assertArrayHasKey('Tunis', $cities);
        $this->assertSame('Tunis', $cities['Tunis']);
        $this->assertArrayHasKey('Sfax', $cities);
        $this->assertSame('Sfax', $cities['Sfax']);
        $this->assertCount(24, $cities);
    }
}

