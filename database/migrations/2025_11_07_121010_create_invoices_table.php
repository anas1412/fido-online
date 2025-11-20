<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('status')->default('draft'); // draft, sent, paid, overdue
            $table->string('currency')->default('TND');

            // --- Exemption Flags ---
            $table->boolean('exonere_tva')->default(false);
            $table->boolean('exonere_rs')->default(false);
            $table->boolean('exonere_tf')->default(false);

            // --- Financials (15, 3 for TND) ---
            $table->decimal('amount_ht', 15, 3)->default(0);
            
            // Snapshots of rates used
            $table->decimal('tva_rate', 5, 2)->default(0);
            $table->decimal('rs_rate', 5, 2)->default(0);
            $table->decimal('tf_value', 15, 3)->default(0); // Stamp is a value, not %

            // Calculated Totals
            $table->decimal('tva_amount', 15, 3)->default(0);
            $table->decimal('rs_amount', 15, 3)->default(0);
            $table->decimal('amount_ttc', 15, 3)->default(0);
            $table->decimal('net_to_pay', 15, 3)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};