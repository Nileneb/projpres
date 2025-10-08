<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Wechselt die Sprache der Anwendung
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLanguage($locale)
    {
        // Überprüfen, ob die angeforderte Sprache unterstützt wird
        if (in_array($locale, ['en', 'de'])) {
            // Sprache in der Session speichern
            Session::put('locale', $locale);
        }

        // Zurück zur vorherigen Seite
        return redirect()->back();
    }
}