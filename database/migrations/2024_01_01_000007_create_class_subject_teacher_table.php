<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subject_teacher', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->year('academic_year');

            // Une matière ne peut être assignée qu'une fois par classe et par année
            $table->unique(['class_id', 'subject_id', 'academic_year'], 'cst_unique');
            $table->index('teacher_id', 'academic_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subject_teacher');
    }
};