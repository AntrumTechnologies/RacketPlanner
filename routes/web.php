<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\CourtRoundController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\MatchDetailsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\PlannerWrapperController;
use App\Http\Controllers\OrganizationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/login', 'auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::view('/register', 'auth.register');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/tournament/invite/{public_link}', [TournamentController::class, 'invite'])->name('tournament-invite');
Route::post('/tournament/invite', [TournamentController::class, 'store_invite'])->name('tournament-store-invite');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/create_permissions', [PermissionController::class, 'create_permissions']);
    Route::get('/elevate', [PermissionController::class, 'assign_admin_permission']);
    Route::get('/superuser', [PermissionController::class, 'assign_superuser_permission']);
    Route::get('/revoke', [PermissionController::class, 'revoke_permissions']);

    /** 
     * Note: order for same type of requests matter!
     * First fixed routes (/tournament/create), then similar routes with variable (/tournament/{id}), finally any parent routes
     */
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations');
    Route::get('/organization/{id}', [OrganizationController::class, 'show'])->name('organization');

    Route::post('/tournament/enroll', [PlayerController::class, 'enroll'])->name('confirm-enroll');
    Route::post('/tournament/withdraw', [PlayerController::class, 'withdraw'])->name('confirm-withdraw');
    Route::get('/tournament/{tournament_id}/enroll', [TournamentController::class, 'enroll'])->name('tournament-enroll');
    Route::get('/tournament/{tournament_id}/withdraw', [TournamentController::class, 'withdraw'])->name('tournament-withdraw');
    Route::get('/tournament/{tournament_id}/leaderboard', [ScoreController::class, 'show'])->name('leaderboard');
    Route::get('/tournament/{tournament_id}', [TournamentController::class, 'show'])->name('tournament');
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');

    Route::get('/matches', [MatchDetailsController::class, 'index'])->name('matches');
    Route::get('/match/{match_id}', [MatchDetailsController::class, 'show'])->name('match');
    Route::post('/match', [MatchDetailsController::class, 'update'])->name('save-score');

    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('edit-user');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user');
    Route::post('/user', [UserController::class, 'update'])->name('update-user');

    Route::get('/admin/organization/{id}', [OrganizationController::class, 'edit'])->name('edit-organization');
    Route::post('/admin/organization/update', [OrganizationController::class, 'update'])->name('update-organization');
    Route::post('/admin/organization/assign_admin', [OrganizationController::class, 'assign_admin_to_organization'])->name('assign-admin');
    Route::post('/admin/organization/remove_admin', [OrganizationController::class, 'remove_admin_from_organization'])->name('remove-admin');
    Route::post('/admin/organization/remove_user', [OrganizationController::class, 'remove_user_from_organization'])->name('remove-user');
    Route::post('/admin/organization/delete', [OrganizationController::class, 'delete'])->name('delete-organization');
    
    Route::get('/admin/tournament/create', [TournamentController::class, 'create'])->name('create-tournament');
    Route::get('/admin/tournament/{tournament_id}', [TournamentController::class, 'edit'])->name('edit-tournament');
    Route::post('/admin/tournament/update', [TournamentController::class, 'update'])->name('update-tournament');
    Route::post('/admin/tournament/delete', [TournamentController::class, 'delete'])->name('delete-tournament');
    Route::post('/admin/tournament/store', [TournamentController::class, 'store'])->name('store-tournament');

    Route::get('/admin/match/{match_id}', [MatchDetailsController::class, 'edit_match'])->name('edit-match');
    Route::post('/admin/match/update', [MatchDetailsController::class, 'update_match'])->name('update-match');
    Route::get('/admin/match/{tournament_id}/{slot_id}', [MatchDetailsController::class, 'create_match'])->name('create-match');
    Route::post('/admin/match/store', [MatchDetailsController::class, 'store_match'])->name('store-match');

    Route::get('/admin/plan/{tournamentId}/generate_schedule', [PlannerWrapperController::class, 'GenerateSchedule'])->name('generate-schedule');
    Route::get('/admin/plan/{tournamentId}/generate_matches', [PlannerWrapperController::class, 'GenerateMatches'])->name('generate-matches');
    Route::get('/admin/plan/{tournamentId}/slot/{slotId}', [PlannerWrapperController::class, 'PlanSlot'])->name('plan-slot');
    Route::get('/admin/plan/{tournamentId}/round/{roundId}', [PlannerWrapperController::class, 'PlanRound'])->name('plan-round');
    Route::get('/admin/plan/{tournamentId}/schedule', [PlannerWrapperController::class, 'PlanSchedule'])->name('plan-schedule');

    Route::get('/admin/plan/{tournamentId}/available/{slotId}', [PlannerWrapperController::class, 'SetSlotStateToAvailable'])->name('slot-available');
    Route::get('/admin/plan/{tournamentId}/clinic/{slotId}', [PlannerWrapperController::class, 'SetSlotStateToClinic'])->name('slot-clinic');
    Route::get('/admin/plan/{tournamentId}/disable/{slotId}', [PlannerWrapperController::class, 'SetSlotStateToDisabled'])->name('slot-disable');

    Route::get('/admin/plan/{tournamentId}/empty_all', [PlannerWrapperController::class, 'EmptyAllSlots'])->name('empty-all-slots');
    Route::get('/admin/plan/{tournamentId}/empty/{slotId}', [PlannerWrapperController::class, 'EmptySlot'])->name('empty-slot');
    Route::get('/admin/plan/{tournamentId}/publish_slot/{slotId}', [PlannerWrapperController::class, 'PublishSlot'])->name('publish-slot');
    Route::get('/admin/plan/{tournamentId}/unpublish_slot/{slotId}', [PlannerWrapperController::class, 'UnpublishSlot'])->name('unpublish-slot');
    Route::get('/admin/plan/{tournamentId}/publish_round/{roundId}', [PlannerWrapperController::class, 'PublishRound'])->name('publish-round');
    Route::get('/admin/plan/{tournamentId}/unpublish_round/{roundId}', [PlannerWrapperController::class, 'UnpublishRound'])->name('unpublish-round');

    Route::get('/admin/tournament/{tournament_id}/players', [PlayerController::class, 'show'])->name('players');
    Route::post('/admin/player/invite', [PlayerController::class, 'invite'])->name('invite-player');
    Route::post('/admin/player/assign', [PlayerController::class, 'store'])->name('assign-player');
    Route::post('/admin/player/manual_add', [PlayerController::class, 'manual_add'])->name('manual-add-player');
    Route::post('/admin/player/remove', [PlayerController::class, 'delete'])->name('remove-player');
    Route::post('/admin/player/present', [PlayerController::class, 'markPresent'])->name('mark-player-present');
    Route::post('/admin/player/absent', [PlayerController::class, 'markAbsent'])->name('mark-player-absent');

    Route::get('/admin/tournament/{tournament_id}/courts_rounds', [CourtRoundController::class, 'show'])->name('courts-rounds');

    Route::view('/admin/tournament/{tournament_id}/court', 'admin.court-create')->name('create-court');
    Route::get('/admin/court/{court_id}', [CourtController::class, 'show'])->name('court');
    Route::post('/admin/court/update', [CourtController::class, 'update'])->name('update-court');
    Route::post('/admin/court/store', [CourtController::class, 'store'])->name('store-court');
    Route::post('/admin/court/delete', [CourtController::class, 'delete'])->name('delete-court');

    Route::view('/admin/tournament/{tournament_id}/round', 'admin.round-create')->name('create-round');
    Route::get('/admin/round/{round_id}', [RoundController::class, 'show'])->name('round');
    Route::post('/admin/round/update', [RoundController::class, 'update'])->name('update-round');
    Route::post('/admin/round/store', [RoundController::class, 'store'])->name('store-round');
    Route::post('/admin/round/delete', [RoundController::class, 'delete'])->name('delete-round');

    Route::get('/admin/users', [UserController::class, 'index'])->name('users');
    Route::view('/admin/user/create', 'admin.user-create')->name('create-user');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('store-user');

    Route::group(['middleware' => ['can:superuser']], function () {
        Route::view('/superuser/organization/create', 'superuser.organization-create')->name('create-organization');
        Route::post('/superuser/organization/store', [OrganizationController::class, 'store'])->name('store-organization');
    });
});
