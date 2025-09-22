<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nba_player_game_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')
                  ->constrained('nba_players')
                  ->onDelete('cascade');

            $table->bigInteger('event_id')->unique(); // unique game identifier

            // New fields for better querying
            $table->date('game_date')->nullable();
            $table->string('opponent_name')->nullable();
            $table->string('opponent_logo')->nullable();
            $table->string('result')->nullable(); // W or L
            $table->string('score')->nullable();  // e.g. "125-118"

            // Stats
            $table->integer('minutes')->nullable();
            $table->string('fg')->nullable();         // field goals made-attempted
            $table->decimal('fg_pct', 5, 2)->nullable();
            $table->string('three_pt')->nullable();   // 3-pointers made-attempted
            $table->decimal('three_pt_pct', 5, 2)->nullable();
            $table->string('ft')->nullable();         // free throws made-attempted
            $table->decimal('ft_pct', 5, 2)->nullable();
            $table->integer('rebounds')->nullable();
            $table->integer('assists')->nullable();
            $table->integer('steals')->nullable();
            $table->integer('blocks')->nullable();
            $table->integer('turnovers')->nullable();
            $table->integer('fouls')->nullable();
            $table->integer('points')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nba_player_game_logs');
    }
};
