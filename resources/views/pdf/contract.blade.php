<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrat de service {{ $orderNumber }}</title>
    <style>
        @page { margin: 60px 50px 70px 50px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.45; color: #222; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 { font-size: 12px; margin: 16px 0 4px; padding-top: 10px; border-top: 1px solid #999; page-break-after: avoid; }
        p, ul { margin: 0 0 6px; }
        blockquote { margin: 6px 0 6px 12px; font-style: italic; }
        .meta { margin-bottom: 8px; }
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 9px; color: #666; }
        .footer .page { float: right; }
        .footer .page:after { content: "Page " counter(page) " sur {{ $totalPages }}"; }
    </style>
</head>
<body>
    <div class="footer">{{ $providerName }} - Contrat de service {{ $template->version }}<span class="page"></span></div>

    <h1>CONTRAT DE SERVICE</h1>
    <div class="meta">
        Commande n° {{ $orderNumber }}<br>
        Date : {{ $orderDate }}<br>
        Version du gabarit : {{ $template->version }} - {{ $templateDate }}
    </div>

    {!! $body !!}
</body>
</html>
