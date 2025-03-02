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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('phone_number')->nullable();
            $table->string('family_status')->nullable();
            $table->string('gender')->nullable();
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();

            $table->string('employment_status')->nullable();
            $table->string('industry')->nullable();
            $table->string('main_source_of_fund')->nullable();
            $table->string('minimum_investment_amount')->nullable();

            $table->softDeletes();
            $table->timestamps();
            //TODO return the database tables name to singular

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
