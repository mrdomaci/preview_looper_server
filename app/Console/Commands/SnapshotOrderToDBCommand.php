<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Connector\Shoptet\OrderDetailResponse;
use App\Connector\Shoptet\OrderPaymentMethodResponse;
use App\Connector\Shoptet\OrderPriceResponse;
use App\Connector\Shoptet\OrderResponse;
use App\Connector\Shoptet\OrderShippingResponse;
use App\Models\ClientService;
use App\Repositories\ClientServiceRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SnapshotOrderToDBCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Snapshot order to DB';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly OrderRepository $orderRepository,
        private readonly OrderProductRepository $orderProductRepository,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $success = true;

        // Get all files in the 'snapshots' directory
        $files = Storage::files('snapshots');

        // Filter and get the latest '_products.gz' file
        $latestFile = collect($files)
            ->filter(fn($file) => str_ends_with($file, '_orders.gz'))
            ->sortByDesc(fn($file) => Storage::lastModified($file))
            ->first();

        if ($latestFile) {
            $clientService = $this->getClientService($latestFile);
            $client = $clientService->client()->first();

            // Unzip the file from .gz to .txt
            $gzFile = gzopen(Storage::path($latestFile), 'rb');
            $txtFilePath = str_replace('.gz', '.txt', $latestFile);
            $txtFile = fopen(Storage::path($txtFilePath), 'wb');

            while (!gzeof($gzFile)) {
                fwrite($txtFile, gzread($gzFile, 4096));
            }

            gzclose($gzFile);
            fclose($txtFile);

            // Loop through the file row by row
            $txtFile = fopen(Storage::path($txtFilePath), 'r');
            DB::beginTransaction();
            try {
                while (($line = fgets($txtFile)) !== false) {
                    $orderData = json_decode($line, true);
                    if ($orderData === null && json_last_error() !== JSON_ERROR_NONE) {
                        // Handle JSON decoding error
                        throw new Exception('Invalid JSON: ' . json_last_error_msg());
                    }
                    $orderResponse = new OrderResponse(
                        $orderData['code'],
                        $orderData['guid'],
                        (isset($orderData['creationTime']) ? new DateTime($orderData['creationTime']) : new DateTime()),
                        (isset($orderData['changeTime']) ? new DateTime($orderData['changeTime']) : null),
                        ($orderData['billingAddress']['fullName'] ?? ''),
                        ($orderData['billingAddress']['company'] ?? null),
                        $orderData['email'],
                        ($orderData['phone'] ?? null),
                        ($orderData['remark'] ?? null),
                        ($orderData['cashDeskOrder'] ?? false),
                        ($orderData['customerGuid'] ?? null),
                        (isset($orderData['paid']) ? (bool) $orderData['paid'] : false),
                        (isset($orderData['status']['id']) ? (string) $orderData['status']['id'] : ''),
                        ($orderData['source']['name'] ?? null),
                        new OrderPriceResponse(
                            (float) $orderData['price']['vat'],
                            (float) $orderData['price']['toPay'],
                            $orderData['price']['currencyCode'],
                            (float) $orderData['price']['withVat'],
                            (float) $orderData['price']['withoutVat'],
                            (float) $orderData['price']['exchangeRate']
                        ),
                        (isset($orderData['paymentMethod']['guid']) && isset($orderData['paymentMethod']['name']) ?
                        new OrderPaymentMethodResponse(
                            $orderData['paymentMethod']['guid'],
                            $orderData['paymentMethod']['name']
                        ) : null),
                        (isset($orderData['shipping']['guid']) && isset($orderData['shipping']['name']) ?
                        new OrderShippingResponse(
                            $orderData['shipping']['guid'],
                            $orderData['shipping']['name']
                        ) : null),
                        ($orderData['adminUrl'] ?? ''),
                    );
                    $order = $this->orderRepository->createOrUpdate($orderResponse, $client);

                    foreach ($orderData['items'] as $item) {
                        if ($item['itemType'] !== 'product') {
                            continue;
                        }
                        $orderDetailResponse = new OrderDetailResponse(
                            $item['productGuid'],
                            (float) $item['amount'],
                        );
                        $this->orderProductRepository->createOrUpdate($orderResponse, $orderDetailResponse, $client, $order);
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Error processing the snapshot file: {$e->getMessage()}");
                $success = false;
            }

            fclose($txtFile);
            Storage::delete($txtFilePath);
            Storage::delete($latestFile);
            $clientService->setSyncedAt(new DateTime());
            $clientService->save();
        } else {
            $this->info('No product snapshot file found.');
        }

        return $success ? Command::SUCCESS : Command::FAILURE;
    }

    private function getClientService(string $filePath): ClientService
    {
        $clientServiceId = (int) explode('_', explode('/', $filePath)[1])[0];
        return $this->clientServiceRepository->get($clientServiceId);
    }
}
