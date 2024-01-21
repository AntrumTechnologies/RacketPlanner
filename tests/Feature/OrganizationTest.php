<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class OrganizationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        if (count(Permission::findByName('superuser')->get()) == 0) {
            Permission::create(['name' => 'superuser']);
        }
    }

    /**
     * Create organization view
     */
    public function test_view_create_organization(): void
    {
        $user = User::factory()->create();

        // Attempt to create org as non-superuser
        $response = $this->actingAs($user)->get('/superuser/organization/create');
        $response->assertStatus(403);

        // Make user superuser and attempt to create org
        $user->givePermissionTo('superuser');
        $response = $this->actingAs($user)->get('/superuser/organization/create');
        $response->assertStatus(200);
    }

    /**
     * Store organization
     */
    public function test_store_organization(): void
    {
        $user = User::factory()->create();

        // Attempt to create org as non-superuser
        $response = $this->actingAs($user)
                        ->post('/superuser/organization/store', ['name' => 'Pied Piper', 'location' => 'Palo Alto']);
        $response->assertStatus(403);

        // Make user superuser and attempt to store org
        $user->givePermissionTo('superuser');
        $response = $this->actingAs($user)
                        ->post('/superuser/organization/store', ['name' => 'Pied Piper', 'location' => 'Palo Alto']);
        $response->assertStatus(302);

        // Attempt to store organization with a similar name
        $response = $this->actingAs($user)
                        ->post('/superuser/organization/store', ['name' => 'Pied Piper', 'location' => 'Palo Alto']);
        $response->assertStatus(302);
    }
}
