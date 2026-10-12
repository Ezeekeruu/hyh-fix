<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every stock change leaves a row here: manual stock-ins, POS
     * checkouts (out), and voided/deleted-sale restocks (in).
     * This is what powers the per-product detail report and the
     * moving-average cost when the same product arrives at
     * different prices.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->unsignedInteger('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
