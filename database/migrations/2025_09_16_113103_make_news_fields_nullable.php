<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeNewsFieldsNullable extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->text('content')->nullable()->change();
            $table->unsignedBigInteger('league_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
            $table->unsignedBigInteger('league_id')->nullable(false)->change();
        });
    }
}
