<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nba_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique(); 
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('abbreviation')->nullable();
            $table->string('logo')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nba_teams');
    }
};
