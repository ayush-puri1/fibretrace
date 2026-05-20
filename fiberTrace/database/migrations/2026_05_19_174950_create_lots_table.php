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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users');
            $table->string('lot_number', 20)->unique();
            $table->enum('category', ['cutting_scraps', 'yarn_ends', 'rejected_batches', 'selvedge']);
            $table->string('fiber_type', 100);
            $table->unsignedTinyInteger('fiber_purity_pct');
            $table->boolean('color_sorted')->default(false);
            $table->string('color_description', 100);
            $table->decimal('weight_kg', 10, 2);
            $table->decimal('base_price', 8, 2);
            $table->enum('status', ['draft', 'pending_review', 'active', 'awarded', 'settled', 'cancelled', 'suspended'])->default('pending_review');
            $table->foreignId('highest_bid_id')->nullable(); // constrained later to bids to avoid circular dependency initially
            $table->timestamp('auction_ends_at')->nullable();
            $table->boolean('flagged')->default(false);
            $table->unsignedSmallInteger('flag_count')->default(0);
            $table->timestamps();
            
            $table->index('status');
            $table->index('seller_id');
            $table->index('fiber_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
