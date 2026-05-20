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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 20)->unique();
            $table->foreignId('lot_id')->constrained();
            $table->foreignId('bid_id')->constrained();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('seller_id')->constrained('users');
            $table->decimal('agreed_price', 8, 2);
            $table->decimal('actual_weight_kg', 10, 2)->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_status', ['pending', 'paid', 'released', 'disputed', 'refunded'])->default('pending');
            $table->text('dispute_reason')->nullable();
            $table->enum('logistics_status', ['ready_for_pickup', 'in_transit', 'delivered', 'confirmed'])->default('ready_for_pickup');
            $table->timestamp('escrow_released_at')->nullable();
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
