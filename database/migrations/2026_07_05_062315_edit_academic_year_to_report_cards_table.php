<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            if (Schema::hasColumn('report_cards', 'student_id')) {
                $table->dropForeign(['student_id']);
            }
            $table->dropUnique('report_cards_student_period_year_unique');
            if (Schema::hasColumn('report_cards', 'period')) {
                $table->dropColumn(['period', 'academic_year']);
            }
            $table->foreignId('term_id')->constrained('terms')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->unique(['student_id', 'term_id', 'academic_year_id'], 'report_cards_student_term_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_cards', function (Blueprint $table) {
            $table->unsignedTinyInteger('period')->comment('1=T1 2=T2 3=T3');
            $table->year('academic_year');
        });
    }
};