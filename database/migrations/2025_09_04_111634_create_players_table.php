<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birthday')->nullable();
            $table->integer('height')->nullable(); 
            $table->string('nationality')->nullable();
            $table->foreignId('league_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('photo')->nullable(); 
            $table->integer('jersey_number')->nullable(); 
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};