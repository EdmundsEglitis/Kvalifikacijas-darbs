<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nba_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();

            $table->date('schedule_date')->nullable();
            $table->dateTime('tipoff')->nullable();
            $table->string('status')->nullable();
            $table->string('venue')->nullable();
            $table->string('city')->nullable();

            $table->unsignedBigInteger('home_team_id')->nullable();
            $table->string('home_team_name')->nullable();
            $table->string('home_team_short')->nullable();
            $table->string('home_team_logo')->nullable();

            $table->unsignedBigInteger('away_team_id')->nullable();
            $table->string('away_team_name')->nullable();
            $table->string('away_team_short')->nullable();
            $table->string('away_team_logo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nba_games');
    }
};
