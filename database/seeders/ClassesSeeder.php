<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe as Classe;

class ClassesSeeder extends Seeder
{
    public function run(): void
    {
        Classe::insert([
            ['name'=>'6ème A','level'=>'6ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'6ème B','level'=>'6ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'5ème A','level'=>'5ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'5ème B','level'=>'5ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'4ème A','level'=>'4ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'4ème B','level'=>'4ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'3ème A','level'=>'3ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'3ème B','level'=>'3ème','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Seconde C','level'=>'Seconde','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Première D','level'=>'Première','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Terminale D','level'=>'Terminale','capacity'=>40,'academic_year'=>2025,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}