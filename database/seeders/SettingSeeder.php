<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Général ──────────────────────────────────────────
            [
                'key'         => 'school_name',
                'value'       => 'Academix School',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Nom de l\'établissement',
            ],
            [
                'key'         => 'school_email',
                'value'       => 'contact@academix.com',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Email officiel de l\'école',
            ],
            [
                'key'         => 'school_phone',
                'value'       => '+228 90 00 00 00',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Téléphone de l\'école',
            ],
            [
                'key'         => 'school_address',
                'value'       => 'Lomé, Togo',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Adresse de l\'école',
            ],
            [
                'key'         => 'school_motto',
                'value'       => 'L\'excellence au service de l\'avenir',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Devise de l\'école',
            ],
            [
                'key'         => 'school_website',
                'value'       => 'https://academix.com',
                'group'       => 'general',
                'type'        => 'string',
                'description' => 'Site web de l\'école',
            ],

            // ── Apparence ─────────────────────────────────────────
            [
                'key'         => 'logo',
                'value'       => '',
                'group'       => 'appearance',
                'type'        => 'file',
                'description' => 'Logo de l\'école',
            ],
            [
                'key'         => 'favicon',
                'value'       => '',
                'group'       => 'appearance',
                'type'        => 'file',
                'description' => 'Favicon du site',
            ],

            // ── Localisation ──────────────────────────────────────
            [
                'key'         => 'currency',
                'value'       => 'FCFA',
                'group'       => 'localization',
                'type'        => 'string',
                'description' => 'Devise utilisée',
            ],
            [
                'key'         => 'language',
                'value'       => 'fr',
                'group'       => 'localization',
                'type'        => 'string',
                'description' => 'Langue de l\'application',
            ],
            [
                'key'         => 'timezone',
                'value'       => 'Africa/Lome',
                'group'       => 'localization',
                'type'        => 'string',
                'description' => 'Fuseau horaire',
            ],

            // ── Académique ────────────────────────────────────────
            [
                'key'         => 'academic_year_format',
                'value'       => 'YYYY – YYYY',
                'group'       => 'academic',
                'type'        => 'string',
                'description' => 'Format d\'affichage de l\'année académique',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}