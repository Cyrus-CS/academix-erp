<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Term;
use App\Models\AcademicYear;

class TermsSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::first();

        Term::insert([
            [
                'academic_year_id' => $year->id,
                'name' => 'Trimestre 1',
                'start_date' => '2025-09-01',
                'end_date' => '2025-12-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'academic_year_id' => $year->id,
                'name' => 'Trimestre 2',
                'start_date' => '2026-01-05',
                'end_date' => '2026-03-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'academic_year_id' => $year->id,
                'name' => 'Trimestre 3',
                'start_date' => '2026-04-01',
                'end_date' => '2026-07-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}