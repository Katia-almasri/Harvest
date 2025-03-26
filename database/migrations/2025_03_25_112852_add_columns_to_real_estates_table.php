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
        Schema::table('real_estates', function (Blueprint $table) {
            $table->unsignedBigInteger('spv_id')->nullable()->after('id');
            $table->foreign('spv_id')->references('id')->on('spv')->onDelete('cascade');
            $table->integer('total_shares');
            $table->integer('shares_sold')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estates', function (Blueprint $table) {
            $table->dropForeign(['spv_id']);
            $table->dropColumn('spv_id');
        });
    }
};
