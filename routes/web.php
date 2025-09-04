<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NbaController;

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

Route::get('/', [NbaController::class, 'home'])->name('home');
Route::get('/players', [NbaController::class, 'allPlayers'])->name('players.index');
// All NBA players
Route::get('/players', [NbaController::class, 'allPlayers'])->name('players');

// Upcoming NBA games
Route::get('/games', [NbaController::class, 'upcomingGames'])->name('games');

// Teams (placeholder route)
Route::get('/teams', function () {
    return view('teams'); // create a teams.blade.php view later
})->name('teams');

// Stats (placeholder route)
Route::get('/stats', function () {
    return view('stats'); // create a stats.blade.php view later
})->name('stats');