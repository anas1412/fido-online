<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            
            // Client Type: 'company' (Société) or 'individual' (Personne physique)
            $table->string('type')->default('company'); 
            
            $table->string('name'); // Company Name or Person Name
            $table->string('contact_person')->nullable(); // Specific person to call
            
            // B2B Requirement
            $table->string('matricule_fiscal')->nullable(); 
            
            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Structured Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            
            $table->text('notes')->nullable(); // Internal notes

            $table->timestamps();
            $table->softDeletes(); // Explicitly requested
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};