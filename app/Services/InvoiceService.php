<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class InvoiceService
{
    public function generate(Order $order): string
    {
        $order->loadMissing('items');

        $taxRate     = config('invoice.tax.rate', 0.20);
        $totalTTC    = (float) $order->total;
        $subtotalHT  = round($totalTTC / (1 + $taxRate), 2);
        $tvaAmount   = round($totalTTC - $subtotalHT, 2);

        $invoiceNumber = $this->invoiceNumber($order);

        $isCanada = strtoupper($order->country) === 'CA';

        $company = config('invoice.company');
        if ($isCanada) {
            $ca = config('invoice.company_ca');
            $company['address_line_1'] = $ca['address_line_1'];
            $company['address_line_2'] = $ca['address_line_2'];
            $company['country']        = $ca['country'];
        }

        $logo      = $this->embedImage(public_path('seoai/images/logo-mark.png'), 'image/png');
        $signature = $this->embedImage(public_path('assets/img/pdf/signature.png'), 'image/png');
        $stamp     = $isCanada
            ? $this->embedImage(public_path('assets/img/pdf/stamp_canada.png'), 'image/png')
            : $this->embedImage(public_path('assets/img/pdf/stamp_france.jpeg'), 'image/jpeg');

        $pdf = Pdf::loadView('pdf.invoice', [
            'order'          => $order,
            'invoiceNumber'  => $invoiceNumber,
            'issueDate'      => $this->frenchDate($order->created_at),
            'taxRate'        => $taxRate,
            'taxLabel'       => config('invoice.tax.label', 'TVA'),
            'subtotalHT'     => $subtotalHT,
            'tvaAmount'      => $tvaAmount,
            'totalTTC'       => $totalTTC,
            'currencySymbol' => $this->currencySymbol($order->currency),
            'currencyLabel'  => $this->currencyLabel($order->currency),
            'paymentMethod'  => $this->paymentMethodLabel($order->payment_gateway),
            'company'        => $company,
            'logo'           => $logo,
            'signature'      => $signature,
            'stamp'          => $stamp,
        ])->setPaper('a4', 'portrait');

        return $pdf->output();
    }

    public function invoiceNumber(Order $order): string
    {
        return implode('-', [
            config('invoice.prefix', 'FAC'),
            $order->created_at->format('Y'),
            $order->created_at->format('md'),
            str_pad($order->id, 3, '0', STR_PAD_LEFT),
        ]);
    }

    public function filename(Order $order): string
    {
        return $this->invoiceNumber($order) . '.pdf';
    }

    private function frenchDate(Carbon $date): string
    {
        $months = [
            1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
            'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre',
        ];

        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }

    private function paymentMethodLabel(string $gateway): string
    {
        return match ($gateway) {
            Order::PAYMENT_GATEWAY_STRIPE,
            Order::PAYMENT_GATEWAY_BRAINTREE,
            Order::PAYMENT_GATEWAY_SQUARE  => 'Carte de crédit',
            Order::PAYMENT_GATEWAY_PAYPAL  => 'PayPal',
            Order::PAYMENT_GATEWAY_PAYUNIT => 'Mobile Money',
            Order::PAYMENT_GATEWAY_WHOP    => 'Whop',
            default                        => ucfirst($gateway),
        };
    }

    private function currencyLabel(string $currency): string
    {
        return match ($currency) {
            'EUR' => 'Euro (EUR)',
            'CAD' => 'Dollar canadien (CAD)',
            'GBP' => 'Livre sterling (GBP)',
            'USD' => 'Dollar US (USD)',
            default => $currency,
        };
    }

    private function currencySymbol(string $currency): string
    {
        return match ($currency) {
            'EUR' => '€',
            'CAD' => 'CA$',
            'GBP' => '£',
            default => '$',
        };
    }

    private function embedImage(string $path, string $mime): ?string
    {
        if (!file_exists($path)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}
