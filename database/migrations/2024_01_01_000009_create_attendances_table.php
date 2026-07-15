<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->text('reason')->nullable();
            $table->timestamps();

            // Un élève ne peut avoir qu'un seul enregistrement de présence par jour
            $table->unique(['student_id', 'date']);
            $table->index(['class_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};