<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Repositories\ClientServiceQueueRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SnapshotProductToDBCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshot:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract products from snapshot file to txt files';

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
        $clientServiceStatus = ClientServiceQueueStatusEnum::SNAPSHOT_PRODUCTS;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);

        if ($clientServiceQueue === null) {
            $this->info('No client service in product snapshot queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        // Get all files in the 'snapshots' directory
        $files = Storage::files('snapshots');

        $setFileName = 'snapshots/' . $clientServiceQueue->getClientServiceId() . '_products.gz';
        $latestFile = collect($files)
            ->first(fn($file) => $file === $setFileName);

        if ($latestFile) {
            $clientService->setUpdateInProgress(true);
            $gz = gzopen(Storage::path($latestFile), 'rb');
            $fileIndex = 1;
            $lineCount = 0;
            $buffer = '';

            while (!gzeof($gz)) {
                $line = gzgets($gz);
                $buffer .= $line;
                $lineCount++;

                if ($lineCount % 10000 === 0) {
                    Storage::put('snapshots/' . $fileIndex . '_' . $clientServiceQueue->getClientServiceId() . '_products.txt', $buffer);
                    $buffer = '';
                    $lineCount = 0;
                    $fileIndex++;
                }
            }

            if ($buffer !== '') {
                Storage::put('snapshots/' . $fileIndex . '_' . $clientServiceQueue->getClientServiceId() . '_products.txt', $buffer);
            }

            gzclose($gz);
            $clientService->setUpdateInProgress(false);
            $clientServiceQueue->next();
        }
        return Command::SUCCESS;
    }
}
