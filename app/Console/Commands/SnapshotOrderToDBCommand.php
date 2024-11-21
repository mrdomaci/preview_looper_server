<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Repositories\ClientServiceQueueRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
    protected $description = 'Extract orders from snapshot file to txt files';

    public function __construct(
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
        $clientServiceQueues = $this->clientServiceQueueRepository->getNext($clientServiceStatus, 5);

        if ($clientServiceQueues->isEmpty()) {
            $this->info('No client service in product snapshot queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServiceQueues as $clientServiceQueue) {
            $clientService = $clientServiceQueue->clientService()->first();
            // Get all files in the 'snapshots' directory
            $files = Storage::files('snapshots');
    
            $setFileName = 'snapshots/' . $clientServiceQueue->getClientServiceId() . '_orders.gz';
            $latestFile = collect($files)
                ->first(fn($file) => $file === $setFileName);
    
            if ($latestFile) {
                try {
                    $clientService->setUpdateInProgress(true);
                    $this->info('Client service ' . $clientService->getId() . ' snaphot orders');
                    $gz = gzopen(Storage::path($latestFile), 'rb');
                    $fileIndex = 1;
                    $lineCount = 0;
                    $buffer = '';
            
                    while (!gzeof($gz)) {
                        $line = gzgets($gz);
                        $buffer .= $line;
                        $lineCount++;
            
                        if ($lineCount % 2000 === 0) {
                            Storage::put('snapshots/' . $fileIndex . '_' . $clientServiceQueue->getClientServiceId() . '_orders.txt', $buffer);
                            $buffer = '';
                            $lineCount = 0;
                            $fileIndex++;
                        }
                    }
            
                    if ($buffer !== '') {
                        Storage::put('snapshots/' . $fileIndex . '_' . $clientServiceQueue->getClientServiceId() . '_orders.txt', $buffer);
                    }
            
                    gzclose($gz);
                    $this->info('Client service ' . $clientService->getId() . ' snapshot orders');
                    $clientServiceQueue->next();
                } catch (Throwable $t) {
                    $this->error('Error updating orders ' . $t->getMessage());
                    $success = false;
                }
                $clientService->setUpdateInProgress(false);
            }
        }
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
