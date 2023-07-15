<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
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
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [UserController::class, 'home'])->name('home');

    /** 
     * Note: order for same type of requests matter!
     * First fixed routes (/tournament/create), then similar routes with variable (/tournament/{id}), finally any parent routes
     */
    Route::view('/admin/tournament/create', 'tournament-create')->name('create-tournament');
    Route::get('/admin/tournament/{id}/details', [TournamentController::class, 'showDetails'])->name('tournament-details');
    Route::get('/admin/tournament/{id}/players', [TournamentController::class, 'tournamentUsers'])->name('tournament-users');
    Route::get('/admin/tournament/{id}/courts', [TournamentController::class, 'tournamentCourts'])->name('tournament-courts');
    Route::get('/admin/tournament/{id}', [TournamentController::class, 'show'])->name('tournament');
    Route::get('/admin/tournaments', [TournamentController::class, 'index'])->name('tournaments');
    
    Route::post('/admin/tournament/update', [TournamentController::class, 'update'])->name('update-tournament-details');
    Route::post('/admin/tournament/delete', [TournamentController::class, 'delete'])->name('delete-tournament');
    Route::post('/admin/tournament/create', [TournamentController::class, 'store'])->name('store-tournament');

    Route::post('/admin/tournament/court', [TournamentController::class, 'assignCourt'])->name('tournament-assign-court');
    Route::post('/admin/tournament/user', [TournamentController::class, 'assignUser'])->name('tournament-assign-user');
    Route::post('/admin/tournament/court/remove', [TournamentController::class, 'removeCourt'])->name('tournament-remove-court');
    Route::post('/admin/tournament/user/remove', [TournamentController::class, 'removeUser'])->name('tournament-remove-user');

    Route::view('/admin/court/create', 'admin.court-create')->name('create-court');
    Route::get('/admin/court/{id}', [CourtController::class, 'show'])->name('court-details');
    Route::get('/admin/courts', [CourtController::class, 'index'])->name('courts');

    Route::post('/admin/court/update', [CourtController::class, 'update'])->name('update-court-details');
    Route::post('/admin/court/create', [CourtController::class, 'store'])->name('store-court');

    Route::get('/admin/users', [UserController::class, 'index'])->name('users');
    Route::get('/admin/user/{id}', [UserController::class, 'show'])->name('user-details');

    Route::post('/admin/user', [UserController::class, 'update'])->name('update-user-details');
});