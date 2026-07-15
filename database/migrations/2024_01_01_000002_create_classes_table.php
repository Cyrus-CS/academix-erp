<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 60);
            $table->string('level', 40);
            $table->smallInteger('capacity')->unsigned()->default(40);
            $table->year('academic_year')->index();
            $table->timestamps();

            $table->unique(['name', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
