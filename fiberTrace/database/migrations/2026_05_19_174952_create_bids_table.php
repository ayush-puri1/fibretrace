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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users');
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['active', 'outbid', 'won', 'cancelled', 'rejected'])->default('active');
            $table->timestamps();
            
            $table->index(['lot_id', 'amount']);
            $table->index('buyer_id');
            $table->unique(['lot_id', 'buyer_id', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
