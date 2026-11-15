<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailTitle ?? config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f4ff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 15px;
            line-height: 1.7;
            color: #696687;
            -webkit-text-size-adjust: 100%;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 32px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(98, 84, 231, 0.10);
        }
        .email-header {
            background-color: #ffffff;
            text-align: center;
            padding: 28px 40px 0;
            border-bottom: 3px solid #6254e7;
        }
        .email-header img {
            max-width: 160px;
            height: auto;
            display: inline-block;
            margin-bottom: 20px;
        }
        .email-body {
            padding: 36px 40px;
        }
        .email-greeting {
            font-size: 17px;
            color: #3b3663;
            margin: 0 0 20px 0;
            font-weight: bold;
        }
        .section-label {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6254e7;
            margin: 28px 0 10px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0dff0;
        }
        .card {
            background-color: #f5f4ff;
            border-radius: 10px;
            padding: 20px 24px;
            margin: 16px 0;
        }
        .detail-row {
            padding: 9px 0;
            border-bottom: 1px solid #e0dff0;
            font-size: 14px;
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: bold;
            color: #3b3663;
            display: inline-block;
            min-width: 140px;
        }
        .detail-value {
            color: #696687;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            font-size: 14px;
        }
        .items-table th {
            background-color: #f5f4ff;
            color: #3b3663;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 10px 12px;
            text-align: left;
            border-bottom: 2px solid #e0dff0;
        }
        .items-table td {
            padding: 11px 12px;
            border-bottom: 1px solid #e0dff0;
            color: #696687;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .items-table tfoot td {
            padding-top: 14px;
            font-weight: bold;
            color: #3b3663;
            font-size: 15px;
            border-top: 2px solid #e0dff0;
        }
        .accent {
            color: #6254e7;
            font-weight: bold;
        }
        .alert-box {
            background-color: #fff8f0;
            border-left: 4px solid #F47514;
            border-radius: 0 8px 8px 0;
            padding: 14px 18px;
            margin: 0 0 20px 0;
            font-size: 14px;
            color: #3b3663;
        }
        .btn-primary {
            display: inline-block;
            background-color: #6254e7;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            margin: 20px 0 4px 0;
        }
        .email-footer {
            background-color: #2f3391;
            padding: 24px 40px;
            text-align: center;
        }
        .email-footer p {
            margin: 4px 0;
            color: rgba(255,255,255,0.65);
            font-size: 12px;
            line-height: 1.6;
        }
        .email-footer a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
        }
        @media only screen and (max-width: 620px) {
            .email-wrapper { margin: 0 !important; border-radius: 0 !important; }
            .email-body, .email-header, .email-footer { padding-left: 20px !important; padding-right: 20px !important; }
            .detail-label { display: block; min-width: unset; margin-bottom: 2px; }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-header">
        <table role="presentation" align="center" cellpadding="0" cellspacing="0" style="margin: 0 auto 20px;">
            <tr>
                <td style="vertical-align: middle; padding-right: 10px;">
                    <img src="{{ asset('seoai/images/logo-mark.png') }}" alt="" width="40" height="40" style="width: 40px; height: 40px; margin: 0; display: block;">
                </td>
                <td style="vertical-align: middle; font-family: 'Sora', Arial, Helvetica, sans-serif; font-size: 22px; line-height: 1; color: #101828; letter-spacing: -0.02em;">
                    <strong style="color: #794AFF;">GBP</strong> Secure Local
                </td>
            </tr>
        </table>
    </div>
    <div class="email-body">
