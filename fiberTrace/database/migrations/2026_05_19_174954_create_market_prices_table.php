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
        Schema::create('market_prices', function (Blueprint $table) {
            $table->id();
            $table->string('fiber_category', 100);
            $table->string('sub_label', 50);
            $table->decimal('price_per_kg', 8, 2);
            $table->decimal('previous_price', 8, 2)->nullable();
            $table->date('week_start');
            $table->foreignId('published_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['week_start', 'fiber_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_prices');
    }
};
