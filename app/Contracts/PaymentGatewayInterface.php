<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createPayment(Order $order, array $paymentData): array;

    public function capturePayment(string $transactionId): array;

    public function refundPayment(string $transactionId, float $amount): array;

    public function getClientToken(): ?string;

    public function verifyWebhook(Request $request): bool;
}
