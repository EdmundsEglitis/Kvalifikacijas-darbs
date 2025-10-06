<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_game_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');

            $table->string('minutes')->nullable();
            $table->integer('points')->default(0);

            $table->integer('fgm2')->default(0); 
            $table->integer('fga2')->default(0); 

            $table->integer('fgm3')->default(0);
            $table->integer('fga3')->default(0); 

            $table->integer('ftm')->default(0);  
            $table->integer('fta')->default(0);  

            $table->integer('oreb')->default(0); 
            $table->integer('dreb')->default(0); 
            $table->integer('reb')->default(0); 

            $table->integer('ast')->default(0);  
            $table->integer('tov')->default(0);
            $table->integer('stl')->default(0); 
            $table->integer('blk')->default(0);  
            $table->integer('pf')->default(0);  

            $table->integer('eff')->default(0); 
            $table->integer('plus_minus')->default(0);

            $table->enum('status', ['played', 'dnp'])->default('played');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_game_stats');
    }
};
