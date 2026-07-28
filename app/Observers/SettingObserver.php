<?php

namespace App\Observers;

use App\Models\Setting;
use App\Services\SettingService;

class SettingObserver
{
    public function __construct(
        private readonly SettingService $settingService
    ) {}

    public function saved(Setting $setting): void
    {
        $this->settingService->clearCache();
    }

    public function deleted(Setting $setting): void
    {
        $this->settingService->clearCache();
    }
}