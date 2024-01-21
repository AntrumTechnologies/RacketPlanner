<?php

namespace Tests\Feature;

use App\Models\Round;
use App\Models\Organization;
use App\Models\Tournament;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoundTest extends TestCase
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

        $this->tournament = Tournament::factory()->create();
        // Set organization ID for tournament
        $this->tournament->owner_organization_id = $this->organization->id;
        $this->tournament->save();
    }

    public function tearDown(): void
    {
        $this->superuser->delete();

        parent::tearDown();
    }

    public function test_add_round(): void
    {
        $starttime = fake()->time('H:i');
        $response = $this->actingAs($this->superuser)->post('/admin/round/store', [
                'name' => 'Round 12',
                'tournament_id' => $this->tournament->id,
                'starttime' => $starttime,
            ]);

        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully added the round Round 12');

        $round = Round::orderBy('id', 'DESC')->first();
        $this->assertEquals($round->name, 'Round 12');
        $this->assertEquals($round->tournament_id, $this->tournament->id);
        $this->assertEquals($round->starttime, $starttime);
    }

    public function test_show_round_view(): void
    {
        $round = Round::orderBy('id', 'DESC')->first();
        $response = $this->actingAs($this->superuser)->get('/admin/round/'. $round->id);
        $response->assertStatus(200);
        $response->assertSee($round->starttime);
    }

    public function test_update_round(): void
    {
        $round = Round::orderBy('id', 'DESC')->first();
        $round_name = fake()->words(1, true);
        $round_starttime = fake()->time('H:i');

        $response = $this->actingAs($this->superuser)->post('/admin/round/update', [
                'id' => $round->id,
                'name' => $round_name,
                'starttime' => $round_starttime,
            ]);
        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully updated round details for '. $round_name);
    }

    public function test_delete_round(): void
    {
        $round = Round::orderBy('id', 'DESC')->first();

        $response = $this->actingAs($this->superuser)->post('/admin/round/delete', [
                'id' => $round->id,
                'tournament_id' => $round->tournament_id,
            ]);
        $response->assertStatus(302);
        $this->followRedirects($response)->assertSee('Successfully deleted the round '. $round->name);

        // Assert schedule was updated as well
        $schedule_count = Schedule::where('tournament_id', $round->tournament_id)
            ->where('round_id', $round->id)->count();
        $this->assertEquals($schedule_count, 0);

        // Assert 'change to courts & rounds' is propagated
        $tournament = Tournament::find($round->tournament_id);
        $this->assertEquals($tournament->change_to_courts_rounds, true);
    }
}
