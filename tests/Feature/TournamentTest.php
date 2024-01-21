<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class TournamentTest extends TestCase
{
    private $organization;
    private $user;
    private $admin;
    private $superuser;

    public function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::factory()->create();

        $this->user = User::factory()->create();

        $this->admin = User::factory()->create();

        $this->superuser = User::factory()->create();
        if (count(Permission::findByName('superuser')->get()) == 0) {
            Permission::create(['name' => 'superuser']);
        }
        $this->superuser->givePermissionTo('superuser');
    }

    public function tearDown(): void
    {
        $this->organization->delete();
        $this->user->delete();
        $this->admin->delete();
        $this->superuser->delete();

        parent::tearDown();
    }

    public function test_view_create_tournament(): void
    {
        // Attempt to access route as a normal user
        $response = $this->actingAs($this->user)->get('/admin/tournament/create');
        $response->assertStatus(401);
    }

    public function test_view_create_tournament_admin_assignment(): void
    {
        // Assign 'admin'-user as admin first
        $response = $this->actingAs($this->superuser)
                        ->post('/admin/organization/assign_admin', ['organization_id' => $this->organization->id, 'email' => $this->admin->email]);
        $response->assertStatus(302);

        $response = $this->actingAs($this->admin)->get('/admin/tournament/create');
        $response->assertStatus(200);
    }

    public function test_view_create_tournament_superuser(): void
    {
        $response = $this->actingAs($this->superuser)->get('/admin/tournament/create');
        $response->assertStatus(200);
    }

    public function test_create_tournament_admin(): void
    {
        // Assign 'admin'-user as admin first
        $response = $this->actingAs($this->superuser)
                        ->post('/admin/organization/assign_admin', ['organization_id' => $this->organization->id, 'email' => $this->admin->email]);
        $response->assertStatus(302);

        $response = $this->actingAs($this->admin)->post('/admin/tournament/store',
            [
                'owner_organization_id' => $this->organization->id,
                'name' => 'Tournament name',
                'datetime_start' => '2024-01-01T12:00',
                'datetime_end' => '2024-01-01T13:00',
                'description' => 'This is a description',
                'location' => 'Some location',
                'location_link' => 'https://racketplanner.nl',
                'max_players' => '42',
                'enroll_until' => '2024-01-01T11:00',
                'double_matches' => true,
            ]);
        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully added the tournament Tournament name');
    }

    public function test_create_tournament_superuser(): void
    {
        $response = $this->actingAs($this->superuser)->post('/admin/tournament/store',
            [
                'owner_organization_id' => $this->organization->id,
                'name' => 'Tournament name 2',
                'datetime_start' => '2024-01-01T12:00',
                'datetime_end' => '2024-01-01T13:00',
                'description' => 'This is a description',
                'location' => 'Some location',
                'location_link' => 'https://racketplanner.nl',
                'max_players' => '42',
                'enroll_until' => '2024-01-01T11:00',
                'double_matches' => true,
            ]);
        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully added the tournament Tournament name');
        
        // $this->followRedirects($response)->dump();
        // ->assertSessionHasInput('status', 'Successfully added the tournament Tournament name 2');
    }

    public function test_view_tournament(): void
    {
        // Create new tournament
        $tournament_name = fake()->name;
        $response = $this->actingAs($this->superuser)->post('/admin/tournament/store',
            [
                'owner_organization_id' => $this->organization->id,
                'name' => $tournament_name,
                'datetime_start' => '2024-01-01T12:00',
                'datetime_end' => '2024-01-01T13:00',
                'description' => 'This is a description',
                'location' => 'Some location',
                'location_link' => 'https://racketplanner.nl',
                'max_players' => '42',
                'enroll_until' => '2024-01-01T11:00',
                'double_matches' => true,
            ]);
        $response->assertStatus(302);

        // Assert view tournament page
        $tournament = Tournament::where('name', $tournament_name)->first();
        $response = $this->actingAs($this->user)->get('/tournament/'. $tournament->id);
        $response->assertStatus(200);
    }

    public function test_view_edit_tournament(): void
    {
        // Create new tournament
        $tournament_name = fake()->name;
        $response = $this->actingAs($this->superuser)->post('/admin/tournament/store',
            [
                'owner_organization_id' => $this->organization->id,
                'name' => $tournament_name,
                'datetime_start' => '2024-01-01T12:00',
                'datetime_end' => '2024-01-01T13:00',
                'description' => 'This is a description',
                'location' => 'Some location',
                'location_link' => 'https://racketplanner.nl',
                'max_players' => '42',
                'enroll_until' => '2024-01-01T11:00',
                'double_matches' => true,
            ]);
        $response->assertStatus(302);

        // Assert view edit tournament page
        $tournament = Tournament::where('name', $tournament_name)->first();
        $response = $this->actingAs($this->superuser)->get('/admin/tournament/'. $tournament->id);
        $response->assertStatus(200);
    }

    public function test_view_update_tournament(): void
    {
        // Create new tournament
        $tournament_name = fake()->name;
        $response = $this->actingAs($this->superuser)->post('/admin/tournament/store',
            [
                'owner_organization_id' => $this->organization->id,
                'name' => 'Test 123',
                'datetime_start' => '2024-01-01T12:00',
                'datetime_end' => '2024-01-01T13:00',
                'description' => 'This is a description',
                'location' => 'Some location',
                'location_link' => 'https://racketplanner.nl',
                'max_players' => '42',
                'enroll_until' => '2024-01-01T11:00',
                'double_matches' => true,
            ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully added the tournament Test 123');

        $tournament = Tournament::where('name', 'Test 123')->first();

        // Assert updated tournament details 
        $response = $this->actingAs($this->superuser)->post('/admin/tournament/update', 
            [
                'id' => $tournament->id,
                'name' => 'I updated my tournament!',
                'datetime_start' => '2024-02-02T06:00',
                'datetime_end' => '2024-02-02T18:00',
                'description' => 'Not interesting',
                'location' => 'Nowhere',
                'location_link' => 'https://example.com',
                'max_players' => 999,
                'enroll_until' => '2024-02-02T00:00',
                'number_of_matches' => 56,
                'partner_rating_tolerance' => 9,
                'team_rating_tolerance' => 4,
                'double_matches' => false,
                'max_match_count' => 983,
            ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully updated the tournament details');

        $tournament = Tournament::where('id', $tournament->id)->first();
        $this->assertEquals($tournament->name, 'I updated my tournament!');
    }

    public function test_delete_tournament(): void
    {
        $tournament = Tournament::all()->first();
        
        $response = $this->actingAs($this->superuser)->post('/admin/tournament/delete', ['id' => $tournament->id]);
        $response->assertStatus(302);
        
        // Check tournament does not exist in database anymore
        $tournament = Tournament::where('id', $tournament->id)->get();
        $this->assertEquals($tournament->toArray(), array());
    }
}
