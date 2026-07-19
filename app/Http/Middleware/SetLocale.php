<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Stancl\Tenancy\Facades\Tenancy;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale');

        if (!$locale) {
            try {
                $tenant = Tenancy::getTenant();
                if ($tenant) {
                    $config = \App\Models\Tenant\DesignConfig::first();
                    if ($config && $config->locale) {
                        $locale = $config->locale;
                    }
                }
            } catch (\Throwable) {
                // Tenancy not initialized (central domain)
            }
        }

        $locale = $locale ?? 'en';

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
