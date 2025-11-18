<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('tva_rate', 5, 2)->default(19.0);
            $table->decimal('rs_rate', 5, 2)->default(3.0);
            $table->decimal('tf_rate', 5, 2)->default(1.0);
            $table->string('site_name')->default('Fido');
            $table->string('support_email')->default('contact@fido.tn');
            $table->string('support_phone')->default('54930048');
            $table->timestamps();
        });

        // Seed a singleton row
        DB::table('settings')->insert([
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
