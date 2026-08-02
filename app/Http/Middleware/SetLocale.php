<?php

namespace App\Http\Middleware;

use Closure;

class SetLocale
{
    /**
     * Apply the locale stored in the session (if any) to the application.
     * Falls back to the config('app.locale') default, which is English.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = session('locale');

        if ($locale && array_key_exists($locale, config('app.available_locales', []))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
