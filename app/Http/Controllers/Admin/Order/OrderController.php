<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Services\ContractService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by customer name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_full_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }
        
        $orders = $query->paginate(20);
        
        // Get status counts for badges
        $statusCounts = [
            'all' => Order::count(),
            'paid' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->count(),
            'pending' => Order::where('payment_status', Order::PAYMENT_STATUS_PENDING)->count(),
            'failed' => Order::where('payment_status', Order::PAYMENT_STATUS_FAILED)->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load('items');

        $invoiceNumber = app(InvoiceService::class)->invoiceNumber($order);

        return view('admin.orders.show', compact('order', 'invoiceNumber'));
    }

    public function invoice(Order $order, InvoiceService $invoices)
    {
        abort_unless($order->payment_status === Order::PAYMENT_STATUS_PAID, 404);

        return response($invoices->generate($order), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$invoices->filename($order).'"',
        ]);
    }

    public function contract(Order $order, ContractService $contracts)
    {
        abort_unless($order->payment_status === Order::PAYMENT_STATUS_PAID, 404);

        return response($contracts->generate($order), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$contracts->filename($order).'"',
        ]);
    }

    public function resendEmail(Request $request, Order $order)
    {
        $request->validate([
            'email' => 'nullable|email|max:255',
        ]);

        $recipient = $request->filled('email') ? $request->email : $order->customer_email;

        $order->load('items');

        Mail::to($recipient)->send(new OrderConfirmation($order));

        return back()->with('success', "Order confirmation email sent to {$recipient}.");
    }
}
