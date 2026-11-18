<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ContractService
{
    /** Placeholders available in the template, shown in the admin editor. */
    public const PLACEHOLDERS = [
        'order_number', 'order_date', 'provider_name', 'provider_address', 'provider_contact',
        'client_name', 'client_email', 'client_phone', 'client_address',
        'service_name', 'service_description', 'total_price', 'total_price_fr', 'currency',
        'privacy_url', 'refund_url', 'policy_version', 'consent_timestamp_line',
    ];

    public function generate(Order $order, ?ContractTemplate $template = null): string
    {
        $template ??= ContractTemplate::active();

        $data = [
            'order' => $order,
            'orderNumber' => $this->orderNumber($order),
            'orderDate' => $order->created_at->format('Y-m-d'),
            'template' => $template,
            'templateDate' => ($template->created_at ?? now())->format('Y-m-d'),
            'providerName' => config('invoice.company.name'),
            'body' => $this->render($order, $template->body),
            'totalPages' => null,
        ];

        // dompdf cannot print the page total itself, so render once to count pages.
        $first = Pdf::loadView('pdf.contract', $data)->setPaper('a4', 'portrait');
        $first->output();
        $data['totalPages'] = $first->getDomPDF()->getCanvas()->get_page_count();

        return Pdf::loadView('pdf.contract', $data)->setPaper('a4', 'portrait')->output();
    }

    /** Replaces whitelisted {{placeholders}}; unknown ones are left untouched. */
    public function render(Order $order, string $body): string
    {
        $order->loadMissing('items.serviceAddon');

        $replacements = [];
        foreach ($this->values($order) as $key => $value) {
            $replacements['{{'.$key.'}}'] = $value;
        }

        return strtr($body, $replacements);
    }

    public function orderNumber(Order $order): string
    {
        // Same number as the invoice, with the invoice prefix swapped for ORD.
        return 'ORD-'.Str::after(app(InvoiceService::class)->invoiceNumber($order), '-');
    }

    public function filename(Order $order): string
    {
        return 'CONTRAT-'.$this->orderNumber($order).'.pdf';
    }

    private function values(Order $order): array
    {
        $item = $order->items->first();
        $addon = $item?->serviceAddon;
        $serviceName = $addon?->getTranslation('name', 'fr', false) ?: $addon?->getTranslation('name', 'en', false) ?: $item?->item_name;
        $serviceDescription = $addon?->getTranslation('description', 'fr', false) ?: $addon?->getTranslation('description', 'en', false) ?: $item?->item_description;
        $company = config('invoice.company');
        $e = fn ($v) => e((string) $v);

        $address = implode(', ', array_filter([
            $order->address_line_1, $order->address_line_2, $order->city,
            $order->state, $order->postal_code, $order->country,
        ]));

        $timestamp = $order->terms_accepted_at
            ? '<p>Horodatage UTC : '.$order->terms_accepted_at->copy()->utc()->format('Y-m-d\TH:i:s.v\Z').'</p>'
            : '';

        return [
            'order_number' => $e($this->orderNumber($order)),
            'order_date' => $e($order->created_at->format('Y-m-d')),
            'provider_name' => $e($company['name']),
            'provider_address' => $e($company['address_line_1'].', '.$company['address_line_2']),
            'provider_contact' => $e(trim(implode(' | ', array_filter([
                settings('contact_email', $company['email']), settings('contact_phone'),
            ])))),
            'client_name' => $e($order->customer_full_name),
            'client_email' => $e($order->customer_email),
            'client_phone' => $e($order->customer_phone),
            'client_address' => $e($address),
            'service_name' => $e($serviceName),
            'service_description' => strip_tags((string) $serviceDescription, '<p><br><ul><ol><li><strong><em>'),
            'total_price' => number_format((float) $order->total, 2, '.', ''),
            'total_price_fr' => number_format((float) $order->total, 2, ',', ''),
            'currency' => $e($order->currency),
            'privacy_url' => $e(route('privacy-policy')),
            'refund_url' => $e(route('refund-policy')),
            'policy_version' => $e($order->policy_version ?? '-'),
            'consent_timestamp_line' => $timestamp,
        ];
    }
}
