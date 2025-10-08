<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Wenn eine Sprache in der Session gespeichert ist, verwende diese
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            App::setLocale($locale);
        } else {
            // Verwende die Standardsprache der Anwendung oder die Browsersprache
            $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? '', 0, 2);
            $locale = in_array($browserLang, ['en', 'de']) ? $browserLang : config('app.locale');
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        // Füge Debug-Information hinzu, wenn APP_DEBUG=true ist
        if (config('app.debug')) {
            $response = $next($request);
            if ($response instanceof \Illuminate\Http\Response) {
                $content = $response->getContent();
                $debugInfo = "<!-- Current locale: " . App::getLocale() . " -->";
                $content = str_replace('</body>', $debugInfo . '</body>', $content);
                $response->setContent($content);
                return $response;
            }
            return $next($request);
        }

        return $next($request);
    }
}
