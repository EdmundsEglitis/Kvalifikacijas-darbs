<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NbaController;
use App\Http\Controllers\LbsController;

// Root = choice between NBA or LBS
Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
})->name('home');

// NBA section
Route::prefix('nba')->group(function () {
    Route::get('/', [NbaController::class, 'home'])->name('nba.home');
    Route::get('/players', [NbaController::class, 'allPlayers'])->name('nba.players');
    Route::get('/games', [NbaController::class, 'upcomingGames'])->name('nba.games.upcoming');
    Route::get('/all-games', [NbaController::class, 'allGames'])->name('nba.games.all');
    Route::get('/games/{id}', [NbaController::class, 'showGame'])->name('nba.games.show');
    Route::get('/teams', fn() => view('nba.teams'))->name('nba.teams');
    Route::get('/stats', fn() => view('nba.stats'))->name('nba.stats');
});

// LBS section
Route::prefix('lbs')->group(function () {
    Route::get('/', [LbsController::class, 'home'])->name('lbs.home');
    Route::get('/league/{id}', [LbsController::class, 'showParent'])->name('lbs.league.show');
    Route::get('/lbs/league/{id}', [LbsController::class, 'showParent'])->name('lbs.league.show');
    Route::get('/lbs/sub-league/{id}', [LbsController::class, 'showSubLeague'])->name('lbs.subleague.show');
    Route::get('/lbs/sub-league/{id}/teams', [LbsController::class, 'showTeams'])->name('lbs.subleague.teams');
    Route::get('/lbl-lbsl', [LbsController::class, 'lblLbsl'])->name('lbs.lbl_lbsl');
    Route::get('/ljbl', [LbsController::class, 'ljbl'])->name('lbs.ljbl');
    Route::get('/izlases', [LbsController::class, 'izlases'])->name('lbs.izlases');
    Route::get('/regionalie-turniri', [LbsController::class, 'regionalieTurniri'])->name('lbs.regionalie');
});
