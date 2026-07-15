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
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['academic_year']); // supprime l’index
            $table->dropColumn('academic_year');
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // $table->year('academic_year')->index();
            $table->dropColumn('academic_year_id');
        });
    }
};