<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearsSeeder extends Seeder
{
    public function run(): void
    {
        AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'is_current' => true,
        ]);
    }
}