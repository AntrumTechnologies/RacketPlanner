<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    public function get_tournaments(): void
    {
        $response = $this->get('/api/tournament');

        $response->assertStatus(200);
    }

    public function get_specific_tournament(): void
    {
        $response = $this->get('/api/tournament/1');

        $response->assertStatus(200);
    }

    public function get_matches_for_specific_tournament(): void
    {
        $response = $this->get('/api/tournament/1/matches');

        $response->assertStatus(200);
    }
}
