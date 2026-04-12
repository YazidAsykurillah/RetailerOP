<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales for the application.
     */
    protected array $supportedLocales = ['en', 'id'];

    /**
     * Handle an incoming request.
     *
     * Set the application locale based on the user's session preference,
     * their stored database preference, or the application default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Check session first (highest priority — most recent user choice)
        if (session()->has('locale')) {
            $locale = session('locale');
        }
        // 2. Check authenticated user's stored preference
        elseif ($request->user() && $request->user()->locale) {
            $locale = $request->user()->locale;
            session(['locale' => $locale]);
        }

        // Validate and apply the locale
        if ($locale && in_array($locale, $this->supportedLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
