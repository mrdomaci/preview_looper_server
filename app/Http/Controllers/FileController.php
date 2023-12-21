<?php

namespace App\Http\Controllers;

use App\Enums\OrderSatusEnum;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class FileController extends Controller
{
    public function orderStatus(string $eshopId, string $fileName): StreamedResponse
    {
        try {
            $client = Client::where('eshop_id', $eshopId)->firstOrFail();
        } catch (Throwable) {
            abort(404);
        }

        try {
            ClientService::where('client_id', $client->getAttribute('id'))->where('service_id', ClientService::ORDER_STATUS)->firstOrFail();
        } catch (Throwable) {
            abort(403);
        }

        $code = explode('.', $fileName)[0];

        try {
            $order = Order::where('client_id', (int) $client->getAttribute('id'))->where('code', $code)->firstOrFail();
        } catch (Throwable) {
            abort(404);
        }
        
        $filePath = storage_path('app/images/order-status/' . $client->getAttribute('id') . '_' . OrderSatusEnum::getIcon($order->getAttribute('status')));

        if (!file_exists($filePath)) {
            abort(404); // File not found
        }
    
        return response()->stream(
            function () use ($filePath) {
                $handle = fopen($filePath, 'rb');
                fpassthru($handle);
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                'Content-Length' => filesize($filePath),
            ]
        );  
    }
}
