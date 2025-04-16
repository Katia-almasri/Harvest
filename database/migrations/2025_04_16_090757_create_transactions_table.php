<?php

use App\Enums\Contract\TransactionStatus;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tx_hash')->nullable();
            $table->string('from_address');
            $table->string('to_address')->nullable();
            $table->unsignedBigInteger('nonce')->nullable();
            $table->string('gas_limit')->nullable();
            $table->string('gas_price')->nullable();
            $table->string('status')->default(TransactionStatus::PENDING);
            $table->unsignedTinyInteger('retries')->default(0);
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
