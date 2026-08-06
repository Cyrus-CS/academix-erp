<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;

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
    public function update(SettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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