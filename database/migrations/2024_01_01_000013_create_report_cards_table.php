<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('period')->comment('1=T1 2=T2 3=T3');
            $table->year('academic_year');
            $table->decimal('average', 5, 2);
            $table->smallInteger('rank')->unsigned()->nullable();
            $table->string('appreciation', 80)->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->timestamps();

            // Un seul bulletin par élève, par trimestre, par année
            $table->unique(['student_id', 'period', 'academic_year'], 'report_cards_student_period_year_unique');
            $table->index(['class_id', 'period', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};