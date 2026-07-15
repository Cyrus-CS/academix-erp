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
        Schema::create('teachers_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('contract_number', 50)->unique();
            $table->enum('contract_type', ['full_time', 'part_time', 'temporary', 'permanent']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 12, 2);
            $table->enum('status', ['active', 'terminated', 'expired'])->default('active');
            $table->text('description')->nullable();
            $table->string('contract_pdf_path', 255)->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index(['teacher_id', 'status']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_contracts');
    }
};