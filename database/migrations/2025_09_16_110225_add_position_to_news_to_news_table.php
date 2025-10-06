<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('position')
                  ->default('slot-1')    
                  ->after('league_id');
        });
    }
    
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
