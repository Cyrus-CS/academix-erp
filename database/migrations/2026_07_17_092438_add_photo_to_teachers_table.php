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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('nationality');
            $table->string('address')->nullable()->after('qualification');
            $table->enum('status', ['active', 'inactive', 'on_leave'])->nullable()->after('employee_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('photo');
            $table->dropColumn('address');
            $table->dropColumn('status');
        });
    }
};