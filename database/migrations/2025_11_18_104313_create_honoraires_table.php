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
        Schema::create('honoraires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('honoraire_number')->unique();
            $table->text('object')->nullable();
            $table->decimal('amount_ht')->nullable();
            $table->decimal('amount_ttc')->nullable();
            $table->decimal('tva_rate')->nullable();
            $table->decimal('rs_rate')->nullable();
            $table->decimal('tf_rate')->nullable();
            $table->decimal('total_amount')->nullable();    
            $table->date('issue_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('honoraires');
    }
};
