<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('locale')) {
            // User has explicitly chosen a language
            $locale = Session::get('locale');
        } else {
            // Auto-detect browser locale on first visit
            $locale = $this->detectBrowserLocale($request);
        }
        
        // Validate locale
        if (!in_array($locale, config('app.available_locales'))) {
            $locale = config('app.fallback_locale');
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
    
    /**
     * Detect browser's preferred locale from Accept-Language header
     */
    private function detectBrowserLocale(Request $request): string
    {
        $availableLocales = config('app.available_locales');
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return config('app.locale');
        }
        
        // Parse Accept-Language header (e.g., "fr-FR,fr;q=0.9,en;q=0.8")
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', trim($lang));
            $locale = trim($parts[0]);
            $quality = isset($parts[1]) ? (float) str_replace('q=', '', $parts[1]) : 1.0;
            
            // Extract language code (fr-FR -> fr, en-US -> en)
            $langCode = substr($locale, 0, 2);
            
            $languages[$langCode] = $quality;
        }
        
        // Sort by quality (preference)
        arsort($languages);
        
        // Check if any preferred language matches our available locales
        foreach ($languages as $langCode => $quality) {
            if (in_array($langCode, $availableLocales)) {
                return $langCode;
            }
        }
        
        // No match found, use default locale
        return config('app.locale');
    }
}
