<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Supported locales for the application.
     */
    protected array $supportedLocales = ['en', 'id'];

    /**
     * Switch the application language.
     *
     * Stores the selected locale in the session and optionally
     * persists it to the authenticated user's profile.
     */
    public function switch(Request $request, string $locale)
    {
        // Validate the locale
        if (!in_array($locale, $this->supportedLocales)) {
            abort(400, 'Unsupported language.');
        }

        // Store in session
        session(['locale' => $locale]);

        // Persist to user record if authenticated
        if ($request->user() && $request->user()->locale !== $locale) {
            $request->user()->update(['locale' => $locale]);
        }

        // Set for current request
        App::setLocale($locale);

        return redirect()->back();
    }
}
