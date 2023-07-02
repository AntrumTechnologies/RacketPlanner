<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CourtController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UserController;

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

Route::group(['middleware' => ['auth']], function() {
    Route::view('/', 'home');
    Route::view('/home', 'home'); // TODO: make sure register page redirects to / instead of /home

    /** 
     * Note: order for same type of requests matter!
     * First fixed routes (/tournament/create), then similar routes with variable (/tournament/{id}), finally any parent routes
     */
    Route::view('/tournament/create', 'tournament-create')->name('create-tournament');
    Route::get('/tournament/{id}/details', [TournamentController::class, 'showDetails'])->name('tournament-details');
    Route::get('/tournament/{id}/players', [TournamentController::class, 'tournamentUsers'])->name('tournament-users');
    Route::get('/tournament/{id}/courts', [TournamentController::class, 'tournamentCourts'])->name('tournament-courts');
    Route::get('/tournament/{id}', [TournamentController::class, 'show'])->name('tournament');
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');

    Route::post('/tournament/update', [TournamentController::class, 'update'])->name('update-tournament-details');
    Route::post('/tournament/delete', [TournamentController::class, 'delete'])->name('delete-tournament');
    Route::post('/tournament/create', [TournamentController::class, 'store'])->name('store-tournament');

    Route::post('/tournament/court', [TournamentController::class, 'assignCourt'])->name('tournament-assign-court');
    Route::post('/tournament/user', [TournamentController::class, 'assignUser'])->name('tournament-assign-user');
    Route::post('/tournament/court/remove', [TournamentController::class, 'removeCourt'])->name('tournament-remove-court');
    Route::post('/tournament/user/remove', [TournamentController::class, 'removeUser'])->name('tournament-remove-user');

    Route::view('/court/create', 'court-create')->name('create-court');
    Route::get('/court/{id}', [CourtController::class, 'show'])->name('court-details');
    Route::get('/courts', [CourtController::class, 'index'])->name('courts');

    Route::post('/court/update', [CourtController::class, 'update'])->name('update-court-details');
    Route::post('/court/create', [CourtController::class, 'store'])->name('store-court');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('user-details');

    Route::post('/user', [UserController::class, 'update'])->name('update-user-details');
});