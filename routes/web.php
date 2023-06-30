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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments');
Route::get('/tournaments/{id}', [TournamentController::class, 'show'])->name('tournament-details');
Route::get('/tournaments/{id}/matches', [TournamentController::class, 'showMatches'])->name('tournament-matches');

Route::get('/courts', [CourtController::class, 'index'])->name('courts');

Route::get('/users', [UserController::class, 'index'])->name('users');

Route::get('/tournaments/edit', function() {
    $data = TournamentController::index();
    return view('tournaments', ['tournaments' => $data]);
})->name('edit-tournament');

Route::get('/tournaments/create', function() {
    $data = TournamentController::index();
    return view('tournaments', ['tournaments' => $data]);
})->name('create-tournament');