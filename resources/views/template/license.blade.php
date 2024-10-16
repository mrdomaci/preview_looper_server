<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service License Receipt</title>
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
            margin-bottom: 50px;
        }
        .footer {
            margin-top: 50px;
            font-size: 12px;
            color: #777;
        }
        .invoice-details {
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
        <!-- Header Section -->
        <div class="header">
            <h1>Service License Invoice</h1>
            <p>Invoice for the purchase of a service license</p>
        </div>

        <!-- Invoice Details Section -->
        <div class="invoice-details">
            <table class="invoice-table">
                <tr>
                    <th>Description</th>
                    <th>Price</th>
                </tr>
                <tr>
                    <td>Service License</td>
                    <td>{{ $price }}</td>
                </tr>
            </table>

            <p class="valid-to">Valid until: {{ $created_at }}</p>
            <p class="valid-to">Valid until: {{ $valid_to }}</p>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Service Provider: Example Provider, Inc.</p>
            <p>Address: 1234 Street, City, Country</p>
            <p>Email: support@example.com | Phone: +123 456 789</p>
        </div>
    </div>
</body>
</html>
ą