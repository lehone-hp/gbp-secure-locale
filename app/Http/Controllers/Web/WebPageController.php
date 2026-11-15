<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;

class WebPageController extends Controller
{
    public function index()
    {
        return view('web.index');
    }

    public function termsAndConditions()
    {
        return view('web.terms-and-conditions');
    }

    public function privacyPolicy()
    {
        return view('web.privacy-policy');
    }
    public function refundPolicy()
    {
        return view('web.refund-policy');
    }

    public function legalNotice()
    {
        return view('web.legal-notice');
    }

    public function services()
    {
        return view('web.services.index');
    }

    public function serviceDetail($slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        return view('web.services.show', compact('service'));
    }

    public function checkout()
    {
        return view('web.services.checkout');
    }
}
