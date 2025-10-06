<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
       Schema::create('nba_standings', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('team_id')->index(); 
    $table->string('team_name');
    $table->string('abbreviation', 10)->nullable();

    $table->integer('wins')->nullable();
    $table->integer('losses')->nullable();
    $table->float('win_percent')->nullable();
    $table->integer('playoff_seed')->nullable();
    $table->integer('games_behind')->nullable();

    $table->float('avg_points_for')->nullable();
    $table->float('avg_points_against')->nullable();
    $table->float('point_differential')->nullable();
    $table->integer('points')->nullable();
    $table->integer('points_for')->nullable();
    $table->integer('points_against')->nullable();
    $table->float('division_win_percent')->nullable();
    $table->float('league_win_percent')->nullable();

    $table->integer('streak')->nullable();
    $table->string('clincher')->nullable();


    $table->string('league_standings')->nullable();
    $table->string('home_record')->nullable();
    $table->string('road_record')->nullable();
    $table->string('division_record')->nullable();
    $table->string('conference_record')->nullable();
    $table->string('last_ten')->nullable();

    $table->integer('season')->nullable();

    $table->timestamps();
});

    }


    public function down(): void
    {
        Schema::dropIfExists('nba_standings');
    }
};
