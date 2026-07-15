<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->string('matricule', 30)->unique();
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female'])->index();
            $table->string('photo_path', 255)->nullable();
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->year('academic_year')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};