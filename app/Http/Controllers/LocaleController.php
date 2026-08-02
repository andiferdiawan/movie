<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch the UI language and remember it in the session.
     */
    public function switch(string $locale, Request $request)
    {
        if (array_key_exists($locale, config('app.available_locales', []))) {
            session(['locale' => $locale]);
        }

        return redirect()->back();
    }
}
