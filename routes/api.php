<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CourtController;
use App\Http\Controllers\MatchDetailsController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Auth::routes(); // TODO: check whether routes should/can go through Sanctum instead

Route::get('/court', [CourtController::class, 'index']);
Route::get('/court/{id}', [CourtController::class, 'show']);
Route::post('/court/create', [CourtController::class, 'store']);
Route::post('/court', [CourtController::class, 'update']);
Route::post('/court/delete', [CourtController::class, 'delete']);

Route::get('/match/{id}', [MatchDetailsController::class, 'show']);
Route::post('/match/create', [MatchDetailsController::class, 'store']);
Route::post('/match', [MatchDetailsController::class, 'update']);
Route::post('/match/delete', [MatchDetailsController::class, 'delete']);

Route::get('/tournament', [TournamentController::class, 'index']);
Route::get('/tournament/{id}', [TournamentController::class, 'show']);
Route::get('/tournament/{id}/matches', [TournamentController::class, 'showMatches']);
Route::post('/tournament/create', [TournamentController::class, 'store']);
Route::post('/tournament/court', [TournamentController::class, 'matchCourts']);
Route::post('/tournament/user', [TournamentController::class, 'matchUsers']);
Route::post('/tournament', [TournamentController::class, 'update']);
Route::post('/tournament/delete', [TournamentController::class, 'delete']);

Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::post('/user', [UserController::class, 'update']);