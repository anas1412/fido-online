<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('honoraires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            
            $table->string('honoraire_number')->unique();
            $table->text('object')->nullable();
            $table->date('issue_date');

            // Exemption Flags
            $table->boolean('exonere_tva')->default(false);
            $table->boolean('exonere_rs')->default(false);
            $table->boolean('exonere_tf')->default(false);

            // Snapshots of rates used at creation
            $table->decimal('tva_rate', 5, 2)->default(0);
            $table->decimal('rs_rate', 5, 2)->default(0);
            $table->decimal('tf_value', 15, 3)->default(0); // Fixed amount (e.g., 1.000)

            // Financial Amounts (15, 3 for Tunisian Dinar precision)
            $table->decimal('amount_ht', 15, 3)->default(0);
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
        Schema::dropIfExists('honoraires');
    }
};