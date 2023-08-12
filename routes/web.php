<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoundController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\MatchDetailsController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\PlannerWrapperController;

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

Auth::routes();
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/create_admin_permission', [PermissionController::class, 'create_admin_permission']);
    Route::get('/elevate', [PermissionController::class, 'assign_admin_permission']);
    Route::get('/revoke', [PermissionController::class, 'revoke_admin_permission']);

    /** 
     * Note: order for same type of requests matter!
     * First fixed routes (/tournament/create), then similar routes with variable (/tournament/{id}), finally any parent routes
     */
    Route::get('/tournament/{tournament_id}', [TournamentController::class, 'show'])->name('tournament');
    Route::get('/admin/tournaments', [TournamentController::class, 'index'])->name('tournaments');

    Route::get('/match/{match_id}', [MatchDetailsController::class, 'show'])->name('match');
    Route::post('/match', [MatchDetailsController::class, 'update'])->name('save-score');

    Route::view('/admin/tournament/create', 'admin.tournament-create')->name('create-tournament');
    Route::get('/admin/tournament/{tournament_id}', [TournamentController::class, 'edit'])->name('edit-tournament');
    Route::post('/admin/tournament/update', [TournamentController::class, 'update'])->name('update-tournament');
    Route::post('/admin/tournament/delete', [TournamentController::class, 'delete'])->name('delete-tournament');
    Route::post('/admin/tournament/store', [TournamentController::class, 'store'])->name('store-tournament');

    Route::get('/admin/leaderboard/{tournament_id}', [ScoreController::class, 'show'])->name('leaderboard');

    Route::get('/admin/plan/{tournamentId}/generate_schedule', [PlannerWrapperController::class, 'GenerateSchedule'])->name('generate-schedule');
    Route::get('/admin/plan/{tournamentId}/generate_matches', [PlannerWrapperController::class, 'GenerateMatches'])->name('generate-matches');
    Route::get('/admin/plan/{tournamentId}/slot/{slotId}', [PlannerWrapperController::class, 'PlanSlot'])->name('plan-slot');
    Route::get('/admin/plan/{tournamentId}/round/{roundId}', [PlannerWrapperController::class, 'PlanRound'])->name('plan-round');
    Route::get('/admin/plan/{tournamentId}/schedule', [PlannerWrapperController::class, 'PlanSchedule'])->name('plan-schedule');

    Route::get('/admin/plan/{tournamentId}/available/{slotId}', [PlannerWrapperController::class, 'SetSlotStateToAvailable'])->name('slot-available');
    Route::get('/admin/plan/{tournamentId}/clinic/{slotId}', [PlannerWrapperController::class, 'SetSlotStateToClinic'])->name('slot-clinic');
    Route::get('/admin/plan/{tournamentId}/disable/{slotId}', [PlannerWrapperController::class, 'SetSlotStateToDisabled'])->name('slot-disable');

    Route::get('/admin/plan/{tournamentId}/publish/{slotId}', [PlannerWrapperController::class, 'PublishSlot'])->name('publish-slot');
    Route::get('/admin/plan/{tournamentId}/unpublish/{slotId}', [PlannerWrapperController::class, 'UnpublishSlot'])->name('unpublish-slot');

    Route::get('/admin/tournament/{tournament_id}/players', [PlayerController::class, 'index'])->name('players');
    Route::post('/admin/player/assign', [PlayerController::class, 'store'])->name('assign-player');
    Route::post('/admin/player/remove', [PlayerController::class, 'delete'])->name('remove-player');
    Route::post('/admin/player/present', [PlayerController::class, 'markPresent'])->name('mark-player-present');
    Route::post('/admin/player/absent', [PlayerController::class, 'markAbsent'])->name('mark-player-absent');

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

    Route::get('/user/{id}', [UserController::class, 'show'])->name('user');
    Route::get('/admin/users', [UserController::class, 'index'])->name('users');
    Route::post('/admin/user', [UserController::class, 'update'])->name('update-user');
    Route::view('/admin/user/create', 'admin.user-create')->name('create-user');
    Route::post('/admin/user/store', [UserController::class, 'store'])->name('store-user');
});