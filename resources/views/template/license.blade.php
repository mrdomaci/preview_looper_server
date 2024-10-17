<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('general.invoice') }} #{{ $number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header, .footer {
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            color: #777;
        }
        .logo {
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .supplier, .demander {
            margin-bottom: 30px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th, .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #f8f8f8;
        }
        .total {
            text-align: right;
        }
        .valid-to {
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section with Logo -->
        <div class="header">
            <div class="logo">
                <!-- Logo Placeholder -->
                <img src="data:image/png;base64,{{ $image }}" alt="Logo" style="max-width: 150px;">
            </div>
            <h1>{{ __('general.invoice') }} #{{ $number }}</h1>
            <p>{{ __('general.invoice_info') }}</p>
        </div>

        <!-- Supplier and Demander Details Section -->
        <div class="supplier">
            <h2>{{ __('general.supplier') }}</h2>
            <p>{{ __('general.name') }}: Ing. Jan Marek Slabihoud</p>
            <p>{{ __('general.cin') }}: 02768127</p>
            <p>{{ __('general.address') }}: Na Hádku 1621, Praha - Dubeč, 107 00</p>
            <p>{{ __('general.email') }}: info@slabihoud.cz</p>
        </div>

        <div class="demander">
            <h2>{{ __('general.demander') }}</h2>
            <p>{{ __('general.name') }}: {{ $name }}</p>
            <p>{{ __('general.address') }}: {{ $address }}</p>
            <p>{{ __('general.cin') }}: {{ $cin }}</p>
            <p>{{ __('general.tin') }}: {{ $tin }}</p>
        </div>

        <!-- Invoice Details Section -->
        <div class="invoice-details">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>{{ __('general.product_description') }}</th>
                        <th>{{ __('general.price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ __('general.product_license') }} {{ $valid_to }}.</td>
                        <td>{{ $price }}</td>
                    </tr>
                </tbody>
            </table>
            <p class="valid-to">{{ __('general.created_at') }}: {{ $created_at }}</p>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>{{ __('general.footer_note') }}</p>
        </div>
    </div>
</body>
</html>
