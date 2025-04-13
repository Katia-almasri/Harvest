<?php

use App\Enums\Payment\PaymentMethod;
use App\Enums\Payment\PaymentStatus;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");

            $table->double("amount")->default(0);
            $table->string("currency");

            $table->string('payment_method')->default(PaymentMethod::STRIPE);
            $table->string("status")->default(PaymentStatus::UNPAID);

            $table->morphs('payable');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
