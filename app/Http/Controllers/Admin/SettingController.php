<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settings
    ) {}

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        return view('settings.index', [
            'settings' => $this->settings->all(),
        ]);
    }

    /**
     * Update the settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name'          => ['required', 'string', 'max:200'],
            'school_email'         => ['required', 'email', 'max:200'],
            'school_phone'         => ['nullable', 'string', 'max:20'],
            'school_address'       => ['nullable', 'string', 'max:500'],
            'school_motto'         => ['nullable', 'string', 'max:300'],
            'school_website'       => ['nullable', 'url', 'max:200'],
            'currency'             => ['required', 'in:FCFA,USD,EUR,GBP'],
            'language'             => ['required', 'in:fr,en'],
            'timezone'             => ['required', 'timezone'],
            'academic_year_format' => ['required', 'string', 'max:50'],
            'logo'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'favicon'              => ['nullable', 'image', 'mimes:ico,png,svg', 'max:512'],
        ], [
            'school_name.required'  => "Le nom de l'école est obligatoire.",
            'school_email.required' => "L'email de l'école est obligatoire.",
            'school_email.email'    => "L'email n'est pas valide.",
            'school_website.url'    => "L'URL du site web n'est pas valide.",
            'currency.required'     => 'La devise est obligatoire.',
            'language.required'     => 'La langue est obligatoire.',
            'timezone.required'     => 'Le fuseau horaire est obligatoire.',
            'timezone.timezone'     => 'Le fuseau horaire sélectionné est invalide.',
            'logo.mimes'            => 'Le logo doit être au format JPG, PNG, SVG ou WEBP.',
            'logo.max'              => 'Le logo ne doit pas dépasser 2 Mo.',
            'favicon.mimes'         => 'Le favicon doit être au format ICO, PNG ou SVG.',
            'favicon.max'           => 'Le favicon ne doit pas dépasser 512 Ko.',
        ]);

        // ── Upload logo ────────────────────────────────────────────────
        if ($request->hasFile('logo')) {
            $currentLogo = $this->settings->get('logo');
            if ($currentLogo) {
                Storage::disk('public')->delete($currentLogo);
            }
            $validated['logo'] = $request->file('logo')
                ->store('settings/logo', 'public');
        } else {
            // Conserver l'existant
            unset($validated['logo']);
        }

        // ── Upload favicon ─────────────────────────────────────────────
        if ($request->hasFile('favicon')) {
            $currentFavicon = $this->settings->get('favicon');
            if ($currentFavicon) {
                Storage::disk('public')->delete($currentFavicon);
            }
            $validated['favicon'] = $request->file('favicon')
                ->store('settings/favicon', 'public');
        } else {
            unset($validated['favicon']);
        }

        // ── Persister via le service (avec invalidation du cache) ──────
        $this->settings->set($validated);

        return to_route('settings.index')
            ->with('success', 'Les paramètres ont été enregistrés avec succès.');
    }
}