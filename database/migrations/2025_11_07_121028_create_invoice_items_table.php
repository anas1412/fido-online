<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();
            $table->string('name')->nullable();
            
            $table->integer('quantity')->default(1);
            // 15, 3 Precision for prices
            $table->decimal('unit_price', 15, 3)->default(0);
            $table->decimal('total', 15, 3)->default(0); // Qty * Unit Price
            $table->decimal('tva_rate', 5, 2)->default(19.00); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};