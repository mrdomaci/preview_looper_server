<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use DateTime;
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
        private readonly ClientServiceRepository $clientServiceRepository,
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::DB_ORDERS;
        $clientServices = $this->clientServiceRepository->getForUpdate($clientServiceStatus, 20);

        if ($clientServices->isEmpty()) {
            $this->info('No client service in product snapshot queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServices as $clientService) {
            $clientService->setUpdateInProgress(true);
            $this->info('Client service ' . $clientService->getId() . ' file order started');
            $client = $clientService->client()->first();

            $txtFilePath = collect(Storage::files('snapshots'))->first(function ($files) use ($clientService) {
                return preg_match('/' . $clientService->getId() . '_orders\.txt$/', $files);
            });

            if ($txtFilePath) {
                $txtFile = fopen(Storage::path($txtFilePath), 'r');
                $orders = [];
                $orderProducts = [];
                $count = 0;
                try {
                    while (($line = fgets($txtFile)) !== false) {
                        $orderData = json_decode($line, true);
                        if (!isset($orderData['guid'])) {
                            continue;
                        }
                        if (!isset($orderData['status']['id'])) {
                            continue;
                        }
                        $orders[] = [
                            'client_id' => $client->id,
                            'guid' => $orderData['guid'],
                            'code' => $orderData['code'],
                            'created_at' => (isset($orderData['creationTime']) ? new DateTime($orderData['creationTime']) : new DateTime()),
                            'full_name' => '',
                            'company' => '',
                            'email' => '',
                            'phone' =>  '',
                            'remark' => '',
                            'cash_desk_order' => ($orderData['cashDeskOrder'] ?? false),
                            'customer_guid' => '',
                            'paid' => ($orderData['paid'] ?? false),
                            'foreign_status_id' => ($orderData['status']['id'] ?? ''),
                            'source' => ($orderData['source']['id'] ?? ''),
                            'vat' => 0,
                            'to_pay' => 0,
                            'currency_code' => ($orderData['price']['currencyCode'] ?? ''),
                            'with_vat' => 0,
                            'without_vat' => 0,
                            'exchange_rate' => 0,
                            'payment_method' => '',
                            'shipping' => '',
                            'admin_url' => '',
                        ];

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
                            $orderProducts[] = [
                                'order_guid' => $orderData['guid'],
                                'product_guid' => $item['productGuid'],
                                'client_id' => $client->id,
                                'amount' => (int) $item['amount'],
                            ];
                        }
                        $count++;
                        if ($count % 10 === 0) {
                            $chunkedOrders = array_chunk($orders, 100);
                            foreach ($chunkedOrders as $batch) {
                                DB::transaction(function () use ($batch) {
                                    $sortedBatch = collect($batch)->sortBy('guid')->toArray();
                                    $this->orderRepository->bulkCreateOrUpdate($sortedBatch);
                                }, 5);
                            }
                            $chunkedOrderProducts = array_chunk($orderProducts, 100);
                            foreach ($chunkedOrderProducts as $batch) {
                                DB::transaction(function () use ($batch) {
                                    $sortedBatch = collect($batch)->sortBy('guid')->toArray();
                                    $this->orderProductRepository->bulkCreateOrIgnore($sortedBatch);
                                }, 5);
                            }
                            unset($orderProducts, $orders);

                            $orderProducts = [];
                            $orders = [];
                        }
                    }
                    if (count($orders) > 0) {
                        $this->orderRepository->bulkCreateOrUpdate($orders);
                        $this->orderProductRepository->bulkCreateOrIgnore($orderProducts);
                    }
                    $this->info('Client service ' . $clientService->getId() . ' file order');
                    Storage::delete($txtFilePath);
                } catch (\Throwable $e) {
                    LoggerHelper::log($e->getMessage());
                    $this->error("Error processing the order snapshot file: {$e->getMessage()}");
                    $success = false;
                } finally {
                    fclose($txtFile);
                }
            } else {
                $service = $clientService->service()->first();
                $clientService->setQueueStatus($clientServiceStatus->next($service))
                    ->setOrdersLastSyncedAt(new DateTime())
                    ->save();
                $this->info('Client service ' . $clientService->getId() . ' file order next');
            }
            $clientService->setUpdateInProgress(false);
        }
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
