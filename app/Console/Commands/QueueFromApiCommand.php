<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Connector\Shoptet\Order;
use App\Enums\QueueStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\StringHelper;
use App\Models\Queue;
use App\Repositories\QueueRepository;
use Illuminate\Console\Command;

class QueueFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse queue from API';

    public function __construct(
        private readonly QueueRepository $queueRepository,
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
        $queues = $this->queueRepository->getCompleted($this->getIterationCount());
        /** @var Queue $queue */
        foreach ($queues as $queue) {
            $clientService = $queue->clientService()->first();
            $response = ConnectorHelper::queue($clientService, $queue);
            if (StringHelper::contains($queue->getEndpoint(), (new Order())->getEndpoint())) {
                $domain = 'orders';
            } else {
                $domain = 'products';
            }
            $localFilePath = storage_path('app/snapshots/' . $clientService->getId() . '_' . $domain . '.gz');
            if ($response->getResultUrl() === null) {
                $queue->delete();
                continue;
            }
            $fileContent = file_get_contents($response->getResultUrl());
            if ($fileContent === false) {
                $success = false;
            } else {
                $fileSaved = file_put_contents($localFilePath, $fileContent);
                if ($fileSaved === false) {
                    $success = false;
                }
            }
            $queue->setStatus(QueueStatusEnum::DONE);
            $queue->setResultUrl($response->getResultUrl());
            $queue->save();
        }

        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
