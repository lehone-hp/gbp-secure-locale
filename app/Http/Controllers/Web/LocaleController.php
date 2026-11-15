<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, config('app.available_locales'))) {
            abort(400);
        }
        
        // Store in session
        Session::put('locale', $locale);
        
        // Redirect back
        return redirect()->back();
    }
}
