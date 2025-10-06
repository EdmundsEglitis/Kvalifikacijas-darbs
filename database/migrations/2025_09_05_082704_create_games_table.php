<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date'); 

            $table->foreignId('team1_id')->constrained('teams')->onDelete('cascade');
            $table->foreignId('team2_id')->constrained('teams')->onDelete('cascade');

            $table->string('score')->nullable(); 


            $table->integer('team1_q1')->nullable();
            $table->integer('team2_q1')->nullable();

            $table->integer('team1_q2')->nullable();
            $table->integer('team2_q2')->nullable();

            $table->integer('team1_q3')->nullable();
            $table->integer('team2_q3')->nullable();

            $table->integer('team1_q4')->nullable();
            $table->integer('team2_q4')->nullable();


            $table->foreignId('winner_id')->nullable()->constrained('teams')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
