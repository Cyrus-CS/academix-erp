<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // ADMIN

        $admin = User::create([
            'name' => 'Eben-ezer Sissou',
            'email' => 'admin@academixerp.test',
            'password' => Hash::make('password123'),
            'phone' => '+2290159498554',
            'is_active' => true,
        ]);

        $admin->assignRole('Admin');

        // TEACHERS

        $teachers = [
            'Arnaud Djossou',
            'Marie Gbaguidi',
            'Serge Adjadohoun',
            'Nadia Kiki',
            'Emmanuel Houndjo',
            'Clarisse Houessou',
            'Pascal Hounkpatin',
            'Rosine Kora',
            'Franck Tossa',
            'Judith Ahouansou',
        ];

        foreach ($teachers as $index => $teacher) {

            $user = User::create([
                'name' => $teacher,
                'email' => 'teacher'.($index + 1).'@academixerp.test',
                'password' => Hash::make('password123'),
                'phone' => '97000'.str_pad($index, 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $user->assignRole('Teacher');
        }

        // PARENTS

        for ($i = 1; $i <= 20; $i++) {

            $user = User::create([
                'name' => "Parent {$i}",
                'email' => "parent{$i}@schoolerp.test",
                'password' => Hash::make('password123'),
                'phone' => '96000'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $user->assignRole('Parent');
        }

        // STUDENTS

        for ($i = 1; $i <= 30; $i++) {

            $user = User::create([
                'name' => "Student {$i}",
                'email' => "student{$i}@academixerp.test",
                'password' => Hash::make('password'),
                'phone' => '95000'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $user->assignRole('Student');
        }
    }
}