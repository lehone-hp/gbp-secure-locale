<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CurrencyManager;

class CurrencyController extends Controller
{
    public function switch(CurrencyManager $currencies, string $code)
    {
        $code = strtoupper($code);

        abort_unless($currencies->isSupported($code), 400);

        $currencies->set($code);

        return redirect()->back();
    }
}
