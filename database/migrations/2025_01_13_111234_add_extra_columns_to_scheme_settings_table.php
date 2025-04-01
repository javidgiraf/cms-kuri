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
        Schema::table('scheme_settings', function (Blueprint $table) {
            $table->integer('start_from')->after('due_duration')->nullable();
            $table->integer('end_to')->after('start_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheme_settings', function (Blueprint $table) {
            $table->dropColumn(['start_from', 'end_to']);
        });
    }
};
