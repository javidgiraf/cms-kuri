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
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('scheme_id')->after('subscription_id')->nullable();
            $table->double('subscribe_amount', 18, 2)->after('scheme_id')->nullable();
            $table->dateTime('start_date')->after('subscribe_amount')->nullable();
            $table->dateTime('end_date')->after('start_date')->nullable();
            $table->dateTime('hold_date')->after('end_date')->nullable();
            $table->dateTime('closed_date')->after('hold_date')->nullable();
            $table->double('total_collected_amount')->after('end_date')->nullable();
            $table->foreign('scheme_id')->references('id')->on('schemes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_histories', function (Blueprint $table) {
            $table->dropForeign(['scheme_id']);
            $table->dropColumn(['scheme_id', 'subscribe_amount', 'start_date', 'end_date', 'hold_date', 'closed_date', 'total_collected_amount']);
        });
    }
};
