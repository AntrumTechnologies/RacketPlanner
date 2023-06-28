<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_get_user_details(): void
    {
        // TODO: fix login of user

        $response = $this->get('/api/user');

        $response->assertStatus(200);
    }

    public function test_get_specific_user_details(): void
    {
        $response = $this->get('/api/user/1');

        $response->assertStatus(200);
    }
}
