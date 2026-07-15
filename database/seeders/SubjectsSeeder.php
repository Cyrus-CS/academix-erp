<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        Subject::insert([
            [
                'name' => 'Mathématiques',
                'code' => 'MATH',
                'coefficient' => 4
            ],
            [
                'name' => 'Français',
                'code' => 'FR',
                'coefficient' => 4
            ],
            [
                'name' => 'Anglais',
                'code' => 'ANG',
                'coefficient' => 3
            ],
            [
                'name' => 'Physique-Chimie',
                'code' => 'PC',
                'coefficient' => 4
            ],
            [
                'name' => 'SVT',
                'code' => 'SVT',
                'coefficient' => 3
            ],
            [
                'name' => 'Histoire-Géographie',
                'code' => 'HG',
                'coefficient' => 3
            ],
            [
                'name' => 'Philosophie',
                'code' => 'PHILO',
                'coefficient' => 2
            ],
            [
                'name' => 'Informatique',
                'code' => 'INFO',
                'coefficient' => 2
            ],
            [
                'name' => 'EPS',
                'code' => 'EPS',
                'coefficient' => 1
            ]
        ]);
    }
}