<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2)->default(20.00);
            $table->enum('type', ['devoir', 'interrogation', 'examen']);
            $table->unsignedTinyInteger('period')->comment('1=T1 2=T2 3=T3');
            $table->year('academic_year')->index();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'subject_id', 'period'], 'grades_student_subject_period_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};