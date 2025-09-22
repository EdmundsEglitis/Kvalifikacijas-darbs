<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nba_player_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->unique();
            $table->uuid('guid')->nullable();
            $table->string('uid')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('full_name')->nullable();
            $table->string('display_name')->nullable();
            $table->string('jersey')->nullable();
            $table->json('links')->nullable();
            $table->json('college')->nullable();
            $table->json('college_team')->nullable();
            $table->json('college_athlete')->nullable();
            $table->string('headshot_href')->nullable();
            $table->string('headshot_alt')->nullable();
            $table->json('position')->nullable();
            $table->json('team')->nullable();
            $table->boolean('active')->nullable();
            $table->json('status')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('display_height')->nullable();
            $table->string('display_weight')->nullable();
            $table->string('display_dob')->nullable();
            $table->integer('age')->nullable();
            $table->string('display_jersey')->nullable();
            $table->string('display_experience')->nullable();
            $table->string('display_draft')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nba_player_details');
    }
};
