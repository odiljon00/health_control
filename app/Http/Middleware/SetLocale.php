<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale {
    public function handle($request, Closure $next) {
        // Session'dagi til yoki default
        $locale = Session::get('locale', config('languages.default', 'uz'));

        // Faqat ruxsat etilgan tillar
        $available = array_keys(config('languages.available', []));
        if (!in_array($locale, $available)) {
            $locale = config('languages.default', 'uz');
        }

        App::setLocale($locale);

        return $next($request);
    }
}