<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceAddon;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    function getDashboard()
    {
        $servicesCount = Service::count();
        $addonsCount = ServiceAddon::count();
        
        $ordersCount = Order::count();
        $paidOrdersCount = Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->count();
        $pendingOrdersCount = Order::where('payment_status', Order::PAYMENT_STATUS_PENDING)->count();
        $customersCount = User::where('role', 'user')->count();
        $latestPurchases = Order::with('items')
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'servicesCount', 'addonsCount', 'ordersCount', 'paidOrdersCount',
            'pendingOrdersCount', 'customersCount', 'latestPurchases'
        ));
    }
}
