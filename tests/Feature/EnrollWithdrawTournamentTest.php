<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class EnrollWithdrawTournamentTest extends TestCase
{
    private $organization;
    private $user;
    private $tournament;
    private $superuser;

    public function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();
        $this->tournament = Tournament::factory()->create();
        $this->user = User::factory()->create();

        $this->tournament->owner_organization_id = $this->organization->id;
        $this->tournament->save();
    }

    public function tearDown(): void
    {
        $this->organization->delete();
        $this->tournament->delete();
        $this->user->delete();

        parent::tearDown();
    }

    public function test_enroll_tournament_view(): void
    {
        $response = $this->actingAs($this->user)->get('/tournament/'. $this->tournament->id .'/enroll');

        $response->assertStatus(200)->assertSee('If you want to join this tournament, then click the button below to confirm your participation.');
    }

    public function test_enroll_tournament(): void
    {
        $this->assertGreaterThan(0, $this->tournament->max_players);
        $this->assertGreaterThan('Y-m-d H:i:s', $this->tournament->enroll_until);

        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee($this->tournament->name)->assertSee('You are enrolled in this tournament');
    }

    public function test_enroll_already_enrolled(): void
    {
        $this->assertGreaterThan(1, $this->tournament->max_players);
        $this->assertGreaterThan('Y-m-d H:i:s', $this->tournament->enroll_until);

        // Enroll once
        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee($this->tournament->name)->assertSee('You are enrolled in this tournament');

        // Enroll twice
        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('You are already enrolled');
    }

    public function test_enroll_max_players(): void
    {
        $this->tournament->max_players = 1;
        $this->tournament->save();

        $this->assertEquals(1, $this->tournament->max_players);
        $this->assertGreaterThan('Y-m-d H:i:s', $this->tournament->enroll_until);

        // Enroll once
        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee($this->tournament->name)->assertSee('You are enrolled in this tournament');

        // Enroll twice
        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('You can not enroll anymore. The maximum number of players has been reached');
    }

    public function test_enroll_date_due(): void
    {
        $this->tournament->enroll_until = '1970-01-01 09:08';
        $this->tournament->save();

        $this->assertGreaterThan(0, $this->tournament->max_players);
        $this->assertEquals('1970-01-01 09:08', $this->tournament->enroll_until);

        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('You can not enroll anymore. The enrollment deadline has been reached or the tournament already started');
    }

    public function test_enroll_start_due(): void
    {
        $this->tournament->datetime_start = '1970-01-02 09:08';
        $this->tournament->save();

        $this->assertGreaterThan(0, $this->tournament->max_players);
        $this->assertEquals('1970-01-02 09:08', $this->tournament->datetime_start);

        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('You can not enroll anymore. The enrollment deadline has been reached or the tournament already started');
    }

    public function test_withdraw_tournament_view(): void
    {
        $response = $this->actingAs($this->user)->get('/tournament/'. $this->tournament->id .'/withdraw');

        $response->assertStatus(200)->assertSee('If you want to withdraw from this tournament for whatever reason, then click the button below.');
    }

    public function test_withdraw_tournament(): void
    {
        // Enroll first
        $response = $this->actingAs($this->user)->post('/tournament/enroll', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee($this->tournament->name)->assertSee('You are enrolled in this tournament');

        // Then withdraw
        $response = $this->actingAs($this->user)->get('/tournament/withdraw', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->dump();

        $response->assertStatus(200);
        $this->followRedirects($response)->assertSee('You can not withdraw, because you are not enrolled');
    }

    public function test_withdraw_due_date(): void
    {
        $this->tournament->enroll_until = '1970-01-01 09:08';
        $this->tournament->save();

        $response = $this->actingAs($this->user)->get('/tournament/withdraw', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('You can not withdraw anymore. The withdraw deadline has been reached or the tournament already started');
    }

    public function test_withdraw_due_start(): void
    {
        $this->tournament->datetime_start = '1970-01-02 09:08';
        $this->tournament->save();

        $response = $this->actingAs($this->user)->get('/tournament/withdraw', [
            'tournament_id' => $this->tournament->id,
        ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('You can not withdraw anymore. The withdraw deadline has been reached or the tournament already started');
    }
}
