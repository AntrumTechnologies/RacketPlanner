<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\User;
use App\Models\Organization;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class CourtTest extends TestCase
{
    private $organization;
    private $superuser;
    private $tournament;

    public function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();

        $this->superuser = User::factory()->create();
        if (count(Permission::findByName('superuser')->get()) == 0) {
            Permission::create(['name' => 'superuser']);
        }
        $this->superuser->givePermissionTo('superuser');

        $response = $this->actingAs($this->superuser)->post('/admin/tournament/store',
            [
                'owner_organization_id' => $this->organization->id,
                'name' => 'Tournament for Court tests',
                'datetime_start' => '2024-01-01T12:00',
                'datetime_end' => '2024-01-01T13:00',
                'description' => 'This is a description',
                'location' => 'Some location',
                'location_link' => 'https://racketplanner.nl',
                'max_players' => '42',
                'enroll_until' => '2024-01-01T11:00',
                'double_matches' => true,
            ]);

        $this->tournament = Tournament::where('name', 'Tournament for Court tests')->first();
    }

    public function tearDown(): void
    {
        $this->superuser->delete();

        parent::tearDown();
    }

    public function test_view_manage_courts_and_rounds(): void
    {
        $tournament = Tournament::all()->first();
        $response = $this->actingAs($this->superuser)->get('/admin/tournament/'. $tournament->id .'/courts_rounds');

        $response->assertStatus(200);
    }

    public function test_add_court(): void
    {
        $response = $this->actingAs($this->superuser)->post('/admin/court/store', [
                'name' => 'Court 1',
                'tournament_id' => $this->tournament->id,
            ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully added the court Court 1');
    }

    public function test_edit_court(): void
    {
        $court = Court::where('tournament_id', $this->tournament->id)->first();
        $response = $this->actingAs($this->superuser)->post('/admin/court/update', [
                'id' => $court->id,
                'name' => 'Court 2',
            ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully updated court details for Court 2');
    }

    public function test_delete_court(): void
    {
        $court = Court::where('tournament_id', $this->tournament->id)->first();
        $response = $this->actingAs($this->superuser)->post('/admin/court/delete', [
                'id' => $court->id,
                'tournament_id' => $court->tournament_id,
            ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully deleted court '. $court->name);
    }
}
