<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            
            // Core
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['commercial', 'accounting', 'medical'])->default('commercial');
            $table->string('currency')->default('TND');
            $table->enum('plan', ['free', 'pro'])->default('free'); 
            // Branding
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();

            // Contact Info (Displayed on Invoice)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // Legal Details (Crucial for Tunisia)
            $table->string('matricule_fiscal')->nullable(); // e.g. 1234567/A/M/000
            $table->string('registre_commerce')->nullable();
            
            // Address Components
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();

            // Banking (For getting paid)
            $table->string('bank_name')->nullable(); // e.g. BIAT, Amen Bank
            $table->string('rib')->nullable(); // 20 digits

            $table->timestamps();
            $table->softDeletes(); // Protects against accidental company deletion
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};