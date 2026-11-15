<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Client;

class AchievementController extends Controller
{
    /**
     * Display the achievements page with certifications and clients.
     */
    public function index()
    {
        $certifications = Certification::active()->ordered()->get();
        $clients = Client::active()->ordered()->get();

        return view('web.achievements.index', compact('certifications', 'clients'));
    }
}
