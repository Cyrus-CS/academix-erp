<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcademicYearsSeeder extends Seeder
{
    public function run()
    {
        $years = [];
        $currentYear = Carbon::now()->year;

        for ($i = 1; $i <= 20; $i++) {
            $start = Carbon::create($currentYear - $i, 9, 1); // Septembre
            $end = Carbon::create($currentYear - $i + 1, 6, 30); // Juin suivant

            $years[] = [
                'name' => $start->year . '-' . $end->year,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'is_current' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('academic_years')->insert($years);
    }
}