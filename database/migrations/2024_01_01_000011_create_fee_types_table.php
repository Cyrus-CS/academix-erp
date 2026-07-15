<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->decimal('amount', 10, 2);
            $table->year('academic_year');
            $table->timestamps();

            // Un même type de frais ne peut être défini qu'une fois par année
            $table->unique(['name', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};