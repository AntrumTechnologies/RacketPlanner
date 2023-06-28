<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CourtTest extends TestCase
{
    public function test_get_courts(): void
    {
        $response = $this->get('/api/court');

        $response->assertStatus(200);
    }

    public function test_get_specific_court(): void
    {
        $response = $this->get('/api/court/1');

        $response->assertStatus(200);
    }
}
