<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $supported = array_keys(config('app.supported_locales', []));
        $locale = (string) $request->input('locale', config('app.locale', 'en'));

        if (!in_array($locale, $supported, true)) {
            $locale = config('app.locale', 'en');
        }

        $request->session()->put('locale', $locale);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
            ]);
        }

        return back();
    }
}
