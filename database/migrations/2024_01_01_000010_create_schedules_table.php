<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('0=Lun 1=Mar 2=Mer 3=Jeu 4=Ven');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // Évite les créneaux en double pour une même classe
            $table->unique(['class_id', 'day_of_week', 'start_time'], 'schedules_class_day_time_unique');
            // Évite les conflits d'emploi du temps pour un enseignant
            $table->index(['teacher_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
