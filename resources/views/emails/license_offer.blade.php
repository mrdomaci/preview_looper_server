<?php

use App\Helpers\QrHelper;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", "Noto Sans", "Liberation Sans", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            background-color: #c9efff;
            color: rgb(33, 37, 41);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin-top: 20px;
        }
        .image-placeholder {
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
            display: block;
        }
        .footer {
            text-align: center;
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>{{__('easy-upsell.license_offer_subject')}}</h3>
        <p>{!!__('easy-upsell.pricing_description')!!}</p>
        <p>{{__('easy-upsell.license_renewal_date')}} <b>{{ $clientService->getRenewalDate()->format('d.m. Y') }}</b>.</p>
        <p>{!! __('easy-upsell.performance_utm_check') !!}</p>
        <p>{!! __('easy-upsell.licence_monthly_payment', ['variable' => $clientService->getVariableSymbol()]) !!}</p>
        <img src="{{ QrHelper::requestPayment(200, 'CZ0420100000002601474251', 490, $clientService->getVariableSymbol(), 'CZK') }}" alt="QR Code monthly CZK" class="image-placeholder"/>
        <p>{!!__('easy-upsell.licence_yearly_payment', ['variable' => $clientService->getVariableSymbol()])!!}</p>
        <img src="{{ QrHelper::requestPayment(200, 'CZ0420100000002601474251', 4990, $clientService->getVariableSymbol(), 'CZK') }}" alt="QR Code yearly CZK" class="image-placeholder"/>
        <!-- Call-to-Action Button -->
        <a href="https://{{ $clientService->client()->first()->eshop_name }}/admin/action/marketplace/settings?serviceId=1794" class="btn btn-custom">{{__('easy-upsell.settings')}}</a>
        <p>{{__('easy-upsell.eur_payment_options')}}</p>
        <!-- Footer Section -->
        <div class="footer">
            <p> <a href="mailto:info@slabihoud">info@slabihoud.cz</a></p>
        </div>
    </div>
</body>
</html>
