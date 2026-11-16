<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Facture {{ $invoiceNumber }}</title>
<style>
    @page { size: A4 portrait; margin: 15mm 0 40mm 0; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
        color: #3b3663;
        background: #fff;
    }
    .page {
        padding: 18mm 18mm 12mm 18mm;
        background: #fff;
    }

    /* ── Header ── */
    .hdr-company-name {
        font-size: 20pt;
        font-weight: bold;
        color: #2f3391;
        line-height: 1.1;
    }
    .hdr-tagline {
        font-size: 7.5pt;
        color: #6254e7;
        font-weight: bold;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        margin-top: 3px;
    }
    .hdr-address {
        font-size: 8.5pt;
        color: #696687;
        margin-top: 8px;
    }
    .facture-title {
        font-size: 32pt;
        font-weight: bold;
        color: #2f3391;
        text-align: left;
        line-height: 1;
    }
    .facture-rule {
        border: none;
        border-top: 3px solid #6254e7;
        margin: 8px 0 10px 0;
    }
    .meta-label {
        font-size: 8.5pt;
        color: #696687;
        padding: 3px 10px 3px 0;
        white-space: nowrap;
    }
    .meta-value-accent {
        font-size: 8.5pt;
        font-weight: bold;
        color: #E07A00;
        text-align: right;
        padding: 3px 0;
    }
    .meta-value {
        font-size: 8.5pt;
        color: #3b3663;
        text-align: right;
        padding: 3px 0;
    }

    /* ── Emetteur ── */
    .emetteur-heading {
        font-size: 7.5pt;
        font-weight: bold;
        color: #2f3391;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        border-bottom: 1px solid #e0dff0;
        padding-bottom: 4px;
        margin: 14px 0 7px 0;
    }
    .emetteur-body {
        font-size: 8.5pt;
        color: #696687;
        line-height: 1.7;
    }

    /* ── Client box ── */
    .client-box {
        background: #f5f4ff;
        border: 1px solid #d8d6f0;
        padding: 14px 16px;
        margin: 14px 0;
    }
    .client-heading {
        font-size: 7.5pt;
        font-weight: bold;
        color: #2f3391;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 7px;
    }
    .client-body {
        font-size: 8.5pt;
        color: #3b3663;
        line-height: 1.7;
    }
    .thank-you-body {
        font-size: 8.5pt;
        color: #696687;
        line-height: 1.8;
    }
    .contact-link { color: #6254e7; font-weight: bold; }

    /* ── Section title ── */
    .section-title {
        font-size: 9pt;
        font-weight: bold;
        color: #2f3391;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        border-bottom: 2px solid #2f3391;
        padding-bottom: 5px;
        margin: 16px 0 10px 0;
    }

    /* ── Items table ── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }
    .items-table th {
        background: #2f3391;
        color: #fff;
        padding: 7px 10px;
        text-align: left;
        font-size: 7.5pt;
        font-weight: bold;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .items-table th.th-amount {
        text-align: right;
        color: #FFD580;
    }
    .items-table td {
        padding: 10px 10px;
        border-bottom: 1px solid #e8e7f5;
        vertical-align: top;
        color: #696687;
    }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .item-name {
        font-weight: bold;
        color: #3b3663;
        margin-bottom: 3px;
        font-size: 8.5pt;
    }
    .item-desc {
        font-size: 8pt;
        color: #696687;
        line-height: 1.5;
    }
    .td-invoice-num { font-weight: bold; color: #3b3663; white-space: nowrap; }
    .td-date { color: #696687; white-space: nowrap; font-size: 8pt; }
    .td-amount { text-align: right; font-weight: bold; color: #3b3663; white-space: nowrap; }

    /* ── Totals ── */
    .totals-outer { width: 100%; margin-top: 12px; }
    .totals-inner { width: 100%; font-size: 8.5pt; }
    .totals-inner td { padding: 3px 0; }
    .tot-label { text-align: right; color: #696687; padding-right: 18px; }
    .tot-value { text-align: right; color: #3b3663; white-space: nowrap; width: 110px; }
    .tot-label-bold { text-align: right; color: #3b3663; font-weight: bold; font-size: 11pt; padding-right: 18px; padding-top: 6px; border-top: 2px solid #e0dff0; }
    .tot-value-bold { text-align: right; color: #6254e7; font-weight: bold; font-size: 11pt; white-space: nowrap; width: 110px; padding-top: 6px; border-top: 2px solid #e0dff0; }
    .tot-general-box { background: #2f3391; padding: 10px 14px; margin-top: 6px; }
    .tot-general-label { color: #fff; font-weight: bold; font-size: 11pt; text-align: right; padding-right: 18px; }
    .tot-general-value { color: #FFD580; font-weight: bold; font-size: 13pt; text-align: right; white-space: nowrap; width: 120px; }

    /* ── Paid confirmation ── */
    .paid-section { margin-top: 18px; }
    .paid-text { font-size: 8.5pt; color: #3b3663; line-height: 1.8; }
    .paid-text strong { color: #2f3391; }
    .pour-label { font-size: 8.5pt; color: #696687; }

    /* ── Footer ── */
    .footer { position: fixed; bottom: 0; left: 0; right: 0; background: #2f3391; padding: 14px 18mm 10px 18mm; }
    .pillar-title { font-size: 7.5pt; font-weight: bold; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    .pillar-text { font-size: 7pt; color: rgba(255,255,255,0.7); line-height: 1.45; }
    .footer-copyright { text-align: center; font-size: 7pt; color: rgba(255,255,255,0.55); margin-top: 10px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.15); }
</style>
</head>
<body>

<div class="page">

    {{-- ════ TOP HEADER ════ --}}
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            {{-- Left: logo + company --}}
            <td style="width:55%; vertical-align:top; padding-right:24px;">
                <table cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
                    <tr>
                        @if($logo)
                            <td style="vertical-align:middle; padding-right:10px;">
                                <img src="{{ $logo }}" alt="" style="height:48px; width:48px; display:block;">
                            </td>
                        @endif
                        <td style="vertical-align:middle; font-size:20pt; font-weight:bold; color:#101828;">
                            {{ $company['name'] }}
                        </td>
                    </tr>
                </table>
                <div class="hdr-tagline">{{ $company['tagline'] }}</div>
                <div class="hdr-address" style="margin-top:10px;">
                    {{ $company['address_line_1'] }} &nbsp; <br/>{{ $company['address_line_2'] }}
                </div>
            </td>

            {{-- Right: FACTURE title + meta --}}
            <td style="width:45%; vertical-align:top; text-align:left;">
                <div class="facture-title">FACTURE</div>
                <hr class="facture-rule">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="meta-label">N&#xB0; DE FACTURE&nbsp;:</td>
                        <td class="meta-value-accent">{{ $invoiceNumber }}</td>
                    </tr>
                    <tr><td colspan="2" style="height:5px;"></td></tr>
                    <tr>
                        <td class="meta-label">DATE D&#x2019;&#xC9;MISSION&nbsp;:</td>
                        <td class="meta-value">{{ $issueDate }}</td>
                    </tr>
                    <tr><td colspan="2" style="height:5px;"></td></tr>
                    <tr>
                        <td class="meta-label">MODE DE PAIEMENT&nbsp;:</td>
                        <td class="meta-value-accent">{{ $paymentMethod }}</td>
                    </tr>
                    <tr><td colspan="2" style="height:5px;"></td></tr>
                    <tr>
                        <td class="meta-label">MONNAIE&nbsp;:</td>
                        <td class="meta-value">{{ $currencyLabel }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ════ EMETTEUR ════ --}}
    <div class="emetteur-heading">&#xC9;metteur</div>
    <table width="100%" cellpadding="0" cellspacing="0" class="emetteur-body">
        <tr>
            <td style="width:50%; vertical-align:top; padding-right:16px;">
                <strong style="color:#3b3663;">{{ $company['name'] }}</strong><br>
                {{ $company['address_line_1'] }}<br>
                {{ $company['address_line_2'] }}<br>
                {{ $company['country'] }}
            </td>
            <td style="width:50%; vertical-align:top;">
                <strong style="color:#3b3663;">&nbsp;</strong><br>
                {{ $company['email'] }}<br>
                {{ $company['website'] }}
            </td>
        </tr>
    </table>

    {{-- ════ CLIENT BOX ════ --}}
    <div class="client-box">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width:53%; vertical-align:top; padding-right:16px;
                           border-right:1px solid #d0cef0;">
                    <div class="client-heading">Client</div>
                    <div class="client-body">
                        @if($order->customer_company)
                            <strong>{{ $order->customer_company }}</strong><br>
                        @endif
                        Responsable&nbsp;: {{ $order->customer_full_name }}<br>
                        {{ $order->address_line_1 }}@if($order->address_line_2), {{ $order->address_line_2 }}@endif<br>
                        {{ $order->postal_code }} {{ $order->city }}, {{ $order->country }}<br>
                        {{ $order->customer_email }}
                    </div>
                </td>
                <td style="width:47%; vertical-align:top; padding-left:16px;">
                    <div class="thank-you-body">
                        Nous vous remercions pour votre confiance.<br><br>
                        Voici le d&#xe9;tail des services factur&#xe9;s.<br><br>
                        Pour toute question, n&#x2019;h&#xe9;sitez pas &#xe0; nous contacter &#xe0;<br>
                        <span class="contact-link">{{ $company['email'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ════ SERVICES TABLE ════ --}}
    <div class="section-title">D&#xe9;tail des services</div>
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:13%;">N&#xb0;</th>
                <th style="width:16%;">Date</th>
                <th>Description du service</th>
                <th class="th-amount" style="width:18%; text-align:right;">
                    Montant ({{ $order->currency }})
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td class="td-invoice-num">#{{ $loop->index + 1 }}</td>
                <td class="td-date">{{ $issueDate }}</td>
                <td>
                    <div class="item-name">{{ $item->item_name }}</div>
                    {{--@if($item->item_description)
                        <div class="item-desc">{!! $item->item_description !!}</div>
                    @endif--}}
                </td>
                <td class="td-amount">
                    {{ $currencySymbol }}{{ number_format((float)$item->subtotal, 2, ',', "\u{00A0}") }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ════ TOTALS ════ --}}
    <table class="totals-outer" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:42%;"></td>
            <td style="width:58%; vertical-align:top;">
                <table class="totals-inner" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="tot-label">Sous-total HT</td>
                        <td class="tot-value">
                            {{ $currencySymbol }}{{ number_format($subtotalHT, 2, ',', "\u{00A0}") }}
                        </td>
                    </tr>
                    <tr>
                        <td class="tot-label">
                            {{ $taxLabel }} ({{ (int)round($taxRate * 100) }}%)
                        </td>
                        <td class="tot-value">
                            {{ $currencySymbol }}{{ number_format($tvaAmount, 2, ',', "\u{00A0}") }}
                        </td>
                    </tr>
                    <tr>
                        <td class="tot-label-bold">Total TTC</td>
                        <td class="tot-value-bold">
                            {{ $currencySymbol }}{{ number_format($totalTTC, 2, ',', "\u{00A0}") }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%" cellpadding="0" cellspacing="0" class="tot-general-box">
                                <tr>
                                    <td class="tot-general-label">Total G&#xe9;n&#xe9;ral</td>
                                    <td class="tot-general-value">
                                        {{ $currencySymbol }}{{ number_format($totalTTC, 2, ',', "\u{00A0}") }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ════ PAID CONFIRMATION ════ --}}
    <table width="100%" cellpadding="0" cellspacing="0" class="paid-section">
        <tr>
            <td style="width:50%; vertical-align:middle;">
                <div class="paid-text">
                    Ces montants ont &#xe9;t&#xe9; r&#xe9;gl&#xe9;s en totalit&#xe9;.<br>
                    <strong>Merci pour votre confiance&nbsp;!</strong>
                </div>
            </td>
            <td style="width:50%; vertical-align:bottom; text-align:right;">
                <table cellpadding="0" cellspacing="0" style="margin-left:auto;">
                    <tr>
                        @if($signature)
                        <td style="vertical-align:bottom; padding-right:4px;">
                            <div class="pour-label" style="margin-bottom:12px;">
                                Pour {{ $company['name'] }}
                            </div>
                            <img src="{{ $signature }}" alt="Signature"
                                 style="height:48px; display:block;">
                        </td>
                        @endif
                        {{--@if($stamp)
                        <td style="vertical-align:middle;">
                            <img src="{{ $stamp }}" alt="Cachet officiel"
                                 style="height:78px; display:block; opacity:0.88;">
                        </td>
                        @endif--}}
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</div>{{-- .page --}}

{{-- ════ FOOTER ════ --}}
<footer class="footer">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:25%; vertical-align:top; padding-right:12px;">
                <div class="pillar-title">Visibilit&#xe9;</div>
                <div class="pillar-text">
                    Nous vous aidons &#xe0; &#xea;tre visible l&#xe0; o&#xf9;
                    vos clients vous cherchent.
                </div>
            </td>
            <td style="width:25%; vertical-align:top; padding-right:12px;">
                <div class="pillar-title">Performance</div>
                <div class="pillar-text">
                    Des strat&#xe9;gies efficaces pour des r&#xe9;sultats mesurables.
                </div>
            </td>
            <td style="width:25%; vertical-align:top; padding-right:12px;">
                <div class="pillar-title"> Confiance</div>
                <div class="pillar-text">
                    Un accompagnement transparent et ax&#xe9; sur vos objectifs.
                </div>
            </td>
            <td style="width:25%; vertical-align:top;">
                <div class="pillar-title">Partenariat</div>
                <div class="pillar-text">
                    Plus qu&#x2019;un service, un v&#xe9;ritable partenaire de croissance.
                </div>
            </td>
        </tr>
    </table>
    <div class="footer-copyright">&#xA9; {{ $company['name'] }} &#x2013; Tous droits r&#xe9;serv&#xe9;s.</div>
</footer>

</body>
</html>
