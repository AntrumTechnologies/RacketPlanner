<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MatchTest extends TestCase
{
    public function get_specific_match(): void
    {
        $response = $this->get('/api/match/1');

        $response->assertStatus(200);
    }
}
