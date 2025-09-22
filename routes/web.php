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

    //show routes
    Route::get('/nba/teams/{id}', [NbaController::class, 'showTeam'])->name('nba.team.show');
    Route::get('/nba/players/{id}', [NbaController::class, 'showPlayer'])->name('nba.player.show');





    Route::get('/games', [NbaController::class, 'upcomingGames'])->name('nba.games.upcoming');
    Route::get('/all-games', [NbaController::class, 'allGames'])->name('nba.games.all');
    Route::get('/games/{id}', [NbaController::class, 'showGame'])->name('nba.games.show');
    Route::get('/teams', [NbaController::class, 'allteams'])->name('nba.teams');

    Route::get('/stats', fn() => view('nba.stats'))->name('nba.stats');
});

// LBS section
Route::prefix('lbs')->group(function () {
    Route::get('/', [LbsController::class, 'home'])->name('lbs.home');
    Route::get('/news/{id}', [LbsController::class, 'showNews'])->name('news.show');
    // Parent and sub-leagues
    Route::get('/league/{id}', [LbsController::class, 'showParent'])->name('lbs.league.show');
    Route::get('/sub-league/{id}', [LbsController::class, 'showSubLeague'])->name('lbs.subleague.show');
    
    // Sub-league tabs
    Route::get('/sub-league/{id}', [LbsController::class, 'subleagueNews'])->name('lbs.subleague.news');
    Route::get('/sub-league/{id}/calendar', [LbsController::class, 'subleagueCalendar'])->name('lbs.subleague.calendar');
    Route::get('/sub-league/{id}/teams', [LbsController::class, 'showTeams'])->name('lbs.subleague.teams'); // existing
    Route::get('/sub-league/{id}/stats', [LbsController::class, 'subleagueStats'])->name('lbs.subleague.stats');

    // Team views
    Route::get('/team/{id}', [LbsController::class, 'showTeam'])->name('lbs.team.show');
    Route::get('/team/{team}/games', [LbsController::class, 'teamGames'])->name('lbs.team.games');
    Route::get('/team/{team}/players', [LbsController::class, 'teamPlayers'])->name('lbs.team.players');
    Route::get('/team/{team}/stats', [LbsController::class, 'teamStats'])->name('lbs.team.stats');
    Route::get('/team/{team}/', [LbsController::class, 'teamOverview'])->name('lbs.team.overview');

    // Individual game view
    Route::get('/game/{id}', [LbsController::class, 'showGame'])->name('lbs.game.detail');
    Route::get('/players/{id}', [LbsController::class, 'show'])->name('lbs.player.show');

    // Shortcuts for main categories
    Route::get('/lbl-lbsl', [LbsController::class, 'lblLbsl'])->name('lbs.lbl_lbsl');
    Route::get('/ljbl', [LbsController::class, 'ljbl'])->name('lbs.ljbl');
    Route::get('/izlases', [LbsController::class, 'izlases'])->name('lbs.izlases');
    Route::get('/regionalie-turniri', [LbsController::class, 'regionalieTurniri'])->name('lbs.regionalie');
});
