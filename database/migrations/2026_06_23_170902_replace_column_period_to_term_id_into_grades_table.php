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
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('period');
            $table->dropColumn('academic_year');
            $table->foreignId('term_id')->constrained()->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->unsignedTinyInteger('period')->comment('1=T1 2=T2 3=T3');
            $table->year('academic_year')->index()->after('period');
            $table->dropColumn('term_id');
            $table->dropColumn('academic_year_id');
        });
    }
};