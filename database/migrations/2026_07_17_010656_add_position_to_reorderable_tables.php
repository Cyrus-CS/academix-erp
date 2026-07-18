<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tables qui ont besoin d'une colonne position.
     */
    private array $tables = [
        'subjects',
        'classes',
        'announcements',
        'teachers',
        'users',
        'schedules',
        'report_cards'
        // Ajoute ici les autres tables si besoin
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'position')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->unsignedInteger('position')
                          ->default(0)
                          ->after('id');
                });
            }
        }

        // Initialiser la position avec l'ordre d'insertion actuel
        foreach ($this->tables as $table) {
            $rows = DB::table($table)->orderBy('id')->get();
            foreach ($rows as $index => $row) {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update(['position' => $index + 1]);
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'position')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('position');
                });
            }
        }
    }
};