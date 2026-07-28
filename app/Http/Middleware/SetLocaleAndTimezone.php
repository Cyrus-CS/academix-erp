<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleAndTimezone
{
    public function __construct(
        private readonly SettingService $settings
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $locale   = $this->settings->get('language', 'fr');
        $timezone = $this->settings->get('timezone', 'Africa/Lome');

        app()->setLocale($locale);
        config(['app.timezone' => $timezone]);
        Carbon::setLocale($locale);
        date_default_timezone_set($timezone);

        return $next($request);
    }
}