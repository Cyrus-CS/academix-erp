<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $settings = $this->getSettings();

        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name'        => ['required', 'string', 'max:200'],
            'school_email'       => ['required', 'email'],
            'school_phone'       => ['nullable', 'string', 'max:20'],
            'school_address'     => ['nullable', 'string', 'max:500'],
            'school_motto'       => ['nullable', 'string', 'max:300'],
            'school_website'     => ['nullable', 'url', 'max:200'],
            'currency'           => ['required', 'in:FCFA,USD,EUR,GBP'],
            'language'           => ['required', 'in:fr,en'],
            'timezone'           => ['required', 'timezone'],
            'academic_year_format' => ['required', 'string', 'max:50'],
            'logo'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'favicon'            => ['nullable', 'image', 'mimes:ico,png,svg', 'max:512'],
        ], [
            'school_name.required'  => 'Le nom de l\'école est obligatoire.',
            'school_email.required' => 'L\'email de l\'école est obligatoire.',
            'school_email.email'    => 'L\'email n\'est pas valide.',
            'currency.required'     => 'La devise est obligatoire.',
            'language.required'     => 'La langue est obligatoire.',
            'timezone.required'     => 'Le fuseau horaire est obligatoire.',
            'logo.mimes'            => 'Le logo doit être au format JPG, PNG, SVG ou WEBP.',
            'logo.max'              => 'Le logo ne doit pas dépasser 2 Mo.',
        ]);

        // Upload logo
        if ($request->hasFile('logo')) {
            if ($old = config('school.logo')) {
                Storage::disk('public')->delete($old);
            }
            $validated['logo'] = $request->file('logo')
                ->store('settings', 'public');
        }

        // Upload favicon
        if ($request->hasFile('favicon')) {
            if ($old = config('school.favicon')) {
                Storage::disk('public')->delete($old);
            }
            $validated['favicon'] = $request->file('favicon')
                ->store('settings', 'public');
        }

        // Sauvegarder dans la table settings (key-value)
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => is_string($value) ? $value : json_encode($value)]
                );
            }
        }

        // Vider le cache config
        \Artisan::call('config:cache');

        return to_route('settings.index')
            ->with('success', 'Les paramètres ont été enregistrés avec succès.');
    }

    /**
     * Récupérer tous les paramètres.
     */
    private function getSettings(): array
    {
        $defaults = [
            'school_name'          => config('app.name', 'School ERP'),
            'school_email'         => config('school.email', 'contact@school.com'),
            'school_phone'         => config('school.phone', ''),
            'school_address'       => config('school.address', ''),
            'school_motto'         => config('school.motto', ''),
            'school_website'       => config('school.website', ''),
            'currency'             => config('school.currency', 'FCFA'),
            'language'             => config('app.locale', 'fr'),
            'timezone'             => config('app.timezone', 'Africa/Lome'),
            'academic_year_format' => config('school.year_format', 'YYYY – YYYY'),
            'logo'                 => config('school.logo', ''),
            'favicon'              => config('school.favicon', ''),
        ];

        // Charger depuis la DB si disponible
        if (\Schema::hasTable('settings')) {
            $dbSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
            $defaults = array_merge($defaults, $dbSettings);
        }

        return $defaults;
    }
}