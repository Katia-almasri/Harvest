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
        Schema::create('spv', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->unique();
            $table->string('legal_document')->nullable();
            $table->foreignId('real_estate_id')->constrained('real_estates');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spv');
    }
};
