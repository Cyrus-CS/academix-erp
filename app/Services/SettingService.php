<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingService
{
    private const CACHE_KEY = 'app_settings';
    private const CACHE_TTL = 3600; // 1 heure

    /** Cache mémoire intra-requête */
    private ?array $resolved = null;

    private const DEFAULTS = [
        'school_name'          => 'School ERP',
        'school_email'         => 'contact@school.com',
        'school_phone'         => '',
        'school_address'       => '',
        'school_motto'         => '',
        'school_website'       => '',
        'currency'             => 'FCFA',
        'language'             => 'fr',
        'timezone'             => 'Africa/Lome',
        'academic_year_format' => 'YYYY – YYYY',
        'logo'                 => '',
        'favicon'              => '',
    ];

    /**
     * Tous les settings — protégé contre les erreurs DB/cache.
     */
    public function all(): array
    {
        // 1. Cache mémoire (0 I/O pour les appels répétés dans la même requête)
        if ($this->resolved !== null) {
            return $this->resolved;
        }

        try {
            // 2. Cache fichier (entre les requêtes)
            $this->resolved = Cache::remember(
                self::CACHE_KEY,
                self::CACHE_TTL,
                fn() => $this->loadFromDb()
            );
        } catch (\Throwable $e) {
            // 3. Fallback total si DB ou cache inaccessibles (boot, migrations, etc.)
            Log::warning('SettingService: fallback sur les valeurs par défaut.', [
                'error' => $e->getMessage(),
            ]);
            $this->resolved = self::DEFAULTS;
        }

        return $this->resolved;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    public function set(array $data): void
    {
        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_string($value) ? $value : json_encode($value)]
            );
        }

        $this->clearCache();
    }

    public function clearCache(): void
    {
        $this->resolved = null;

        try {
            Cache::forget(self::CACHE_KEY);
        } catch (\Throwable) {
            // Silencieux
        }
    }

    private function loadFromDb(): array
    {
        // 1 seule requête SQL
        $dbSettings = Setting::query()
            ->select(['key', 'value'])
            ->get()
            ->mapWithKeys(fn($s) => [$s->key => $s->value])
            ->toArray();

        return array_merge(self::DEFAULTS, $dbSettings);
    }
}