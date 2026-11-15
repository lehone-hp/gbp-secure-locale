<?php

namespace App\Http\Controllers\Concerns;

use App\Mail\NewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

trait SendsOrderEmails
{
    protected function sendOrderEmails(Order $order): void
    {
        try {
            Mail::to($order->customer_email)->send(new OrderConfirmation($order));

            $adminEmail = settings()->get('notify_email');
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
        } catch (\Exception $e) {
            \Log::error('Failed to send order emails: ' . $e->getMessage());
        }
    }
}
