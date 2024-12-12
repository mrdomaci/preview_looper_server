<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
        $clientServiceStatus = ClientServiceQueueStatusEnum::SNAPSHOT_PRODUCTS;
        $clientServices = $this->clientServiceRepository->getForUpdate($clientServiceStatus, 5);
        if ($clientServices->isEmpty()) {
            $this->info('No client service in product snapshot queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServices as $clientService) {
            $files = Storage::files('snapshots');

            $filesToDelete = collect($files)
                ->filter(fn($file) => str_ends_with($file, $clientService->getId() . '_products.txt'));

            foreach ($filesToDelete as $file) {
                Storage::delete($file);
            }

            $setFileName = 'snapshots/' . $clientService->getId() . '_products.gz';
            $latestFile = collect($files)
                ->first(fn($file) => $file === $setFileName);

            if ($latestFile) {
                try {
                    $clientService->setUpdateInProgress(true);
                    $this->info('Client service ' . $clientService->getId() . ' snaphot products');
                    $gz = gzopen(Storage::path($latestFile), 'rb');
                    $fileIndex = 1;
                    $lineCount = 0;
                    $buffer = '';
    
                    while (!gzeof($gz)) {
                        $line = gzgets($gz);
                        $buffer .= $line;
                        $lineCount++;
    
                        if ($lineCount % 2000 === 0) {
                            Storage::put('snapshots/' . $fileIndex . '_' . $clientService->getId() . '_products.txt', $buffer);
                            $buffer = '';
                            $lineCount = 0;
                            $fileIndex++;
                        }
                    }
    
                    if ($buffer !== '') {
                        Storage::put('snapshots/' . $fileIndex . '_' . $clientService->getId() . '_products.txt', $buffer);
                    }
    
                    gzclose($gz);
                    Storage::delete($latestFile);
                    $this->info('Client service ' . $clientService->getId() . ' snaphot products');
                    $service = $clientService->service()->first();
                    $clientService->setQueueStatus($clientServiceStatus->next($service));
                    $clientService->save();
                } catch (Throwable $t) {
                    $this->error('Error updating product for client service id: ' . $clientService->getId() . ' ' . $t->getMessage());
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
