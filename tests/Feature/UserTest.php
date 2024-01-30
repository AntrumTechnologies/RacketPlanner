<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function tearDown(): void
    {
        $this->user->delete();

        parent::tearDown();
    }

    public function test_get_user_details(): void
    {
        $response = $this->actingAs($this->user)->get('/user/'. $this->user->id);
        $response->assertStatus(200);
    }

    public function test_get_edit_user_details(): void
    {
        $response = $this->actingAs($this->user)->get('/user/'. $this->user->id.'/edit');
        $response->assertStatus(200);
    }

    public function test_update_edit_user_details(): void
    {
        $response = $this->actingAs($this->user)->post('/user', [
            'id' => $this->user->id,
            'name' => 'Jolige paling',
            'rating' => 4,
        ]);
        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully updated user details');
    }
}
