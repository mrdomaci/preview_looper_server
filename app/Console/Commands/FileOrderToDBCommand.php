<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Connector\Shoptet\OrderDetailResponse;
use App\Connector\Shoptet\OrderPaymentMethodResponse;
use App\Connector\Shoptet\OrderPriceResponse;
use App\Connector\Shoptet\OrderResponse;
use App\Connector\Shoptet\OrderShippingResponse;
use App\Enums\ClientServiceQueueStatusEnum;
use App\Repositories\ClientServiceQueueRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileOrderToDBCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert or update orders from txt files to DB';

    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderProductRepository $orderProductRepository,
        private readonly ClientServiceQueueRepository $clientServiceQueueRepository,
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::SNAPSHOT_ORDERS;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);

        if ($clientServiceQueue === null) {
            $this->info('No client service in product snapshot queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $client = $clientService->client()->first();

        $txtFilePath = collect(Storage::files('snapshots'))->first(function ($files) use ($clientServiceQueue) {
            return preg_match('/' . $clientServiceQueue->client_service_id . '_orders\.txt$/', $files);
        });

        if ($txtFilePath) {
            $txtFile = fopen(Storage::path($txtFilePath), 'r');
            DB::beginTransaction();
            try {
                $clientService->setUpdateInProgress(true);
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
                        ($orderData['email'] ?? null),
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
                        if (!isset($item['productGuid'])) {
                            continue;
                        }
                        if (!isset($item['amount'])) {
                            continue;
                        }
                        $orderDetailResponse = new OrderDetailResponse(
                            $item['productGuid'],
                            (float) $item['amount'],
                        );
                        $this->orderProductRepository->createOrUpdate($orderResponse, $orderDetailResponse, $client, $order);
                    }
                }
                $clientService->setUpdateInProgress(false);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Error processing the snapshot file: {$e->getMessage()}");
                return Command::FAILURE;
            }

            fclose($txtFile);
            Storage::delete($txtFilePath);
        } else {
            $clientServiceQueue->next();
        }
        return Command::SUCCESS;
    }
}
