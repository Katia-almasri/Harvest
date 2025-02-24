<?php

use App\Enums\RealEstate\RealEstateCategory;
use App\Enums\RealEstate\RealEstateStatus;
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
        Schema::create('real_estates', function (Blueprint $table) {
            $table->id();
            $table->string('unique_number');
            $table->string('name');
            $table->string('description')->nullable();
            $table->float('area');
            $table->foreignId('city_id')->onDelete('cascade');
            $table->integer('bedroom_number')->default(2);
            $table->integer('bathroom_number')->default(1);

            $table->string('status')->default(RealEstateStatus::ACTIVE);
            $table->string('category')->default(RealEstateCategory::RESIDENTIAL);
            $table->float('purchase_price')->default(0);

            $table->foreignId('created_by')->onDelete('cascade');

            $table->boolean('has_pool')->default(false);
            $table->boolean('has_garden')->default(false);
            $table->boolean('has_parking')->default(false);
            $table->boolean('close_to_transportation')->default(true);
            $table->boolean('close_to_hospital')->default(true);
            $table->boolean('close_to_school')->default(true);

            $table->string('location')->nullable();
            $table->float('longitude')->nullable();
            $table->float('latitude')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_estates');
    }
};
