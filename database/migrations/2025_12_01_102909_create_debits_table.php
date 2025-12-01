<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            
            // Link to the original invoice (Standard for Note de Débit)
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('honoraire_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('debit_number');
            $table->unique(['tenant_id', 'debit_number']);
            $table->text('object')->nullable(); // "Objet"
            $table->date('issue_date');

            // Exemption Flags
            $table->boolean('exonere_tva')->default(false);
            $table->boolean('exonere_rs')->default(false);
            $table->boolean('exonere_tf')->default(false);

            // Rate Snapshots (Preserve history)
            $table->decimal('tva_rate', 5, 2)->default(0);
            $table->decimal('rs_rate', 5, 2)->default(0);
            $table->decimal('tf_value', 15, 3)->default(0);

            // Financial Amounts (3 decimals for TND)
            $table->decimal('amount_ht', 15, 3)->default(0);
            
            // NEW: Debours (Non-taxable expenses to be reimbursed)
            $table->decimal('debours_amount', 15, 3)->default(0); 
            
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
        Schema::dropIfExists('debits');
    }
};