<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::create('nba_players', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('external_id')->unique(); 
        $table->uuid('guid')->nullable();
        $table->string('uid')->nullable();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('full_name');
        $table->string('display_weight')->nullable();
        $table->string('display_height')->nullable();
        $table->integer('age')->nullable();
        $table->unsignedBigInteger('salary')->nullable();
        $table->string('image')->nullable();


        $table->unsignedBigInteger('team_id')->nullable();
        $table->string('team_name')->nullable();
        $table->string('team_logo')->nullable();

        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('nba_players');
    }
};
