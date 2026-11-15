<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceAddon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    function getDashboard()
    {
        $servicesCount = Service::count();
        $addonsCount = ServiceAddon::count();
        
        return view('admin.dashboard.index', compact('servicesCount', 'addonsCount'));
    }
}
