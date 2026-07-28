<?php

/*
|--------------------------------------------------------------------------
| Configuration de l'établissement
|--------------------------------------------------------------------------
| Ce fichier sert de fallback. Les vraies valeurs sont chargées depuis
| la base de données via SettingService et mises en cache.
|--------------------------------------------------------------------------
*/

return [
    'name'          => env('SCHOOL_NAME', 'School ERP'),
    'email'         => env('SCHOOL_EMAIL', 'contact@school.com'),
    'phone'         => env('SCHOOL_PHONE', ''),
    'address'       => env('SCHOOL_ADDRESS', ''),
    'motto'         => env('SCHOOL_MOTTO', ''),
    'website'       => env('SCHOOL_WEBSITE', ''),
    'currency'      => env('SCHOOL_CURRENCY', 'FCFA'),
    'year_format'   => env('SCHOOL_YEAR_FORMAT', 'YYYY – YYYY'),
    'logo'          => env('SCHOOL_LOGO', ''),
    'favicon'       => env('SCHOOL_FAVICON', ''),
    'current_year'  => env('SCHOOL_CURRENT_YEAR', '2024 – 2025'),
];