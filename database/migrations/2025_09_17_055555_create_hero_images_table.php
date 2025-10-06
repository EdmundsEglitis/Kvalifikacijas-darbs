<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('hero_images', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); 
            $table->string('image_path');
            $table->enum('location', ['home', 'league', 'subleague']);
            $table->unsignedBigInteger('league_id')->nullable(); 
            $table->timestamps();
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_images');
    }
};
