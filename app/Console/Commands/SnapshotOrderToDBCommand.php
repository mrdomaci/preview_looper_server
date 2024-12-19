<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceRepository;
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::SNAPSHOT_ORDERS;
        $clientServices = $this->clientServiceRepository->getForUpdate($clientServiceStatus, 5);

        if ($clientServices->isEmpty()) {
            $this->info('No client service in orders snapshot queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServices as $clientService) {
            $files = Storage::files('snapshots');

            $filesToDelete = collect($files)
                ->filter(fn($file) => str_ends_with($file, $clientService->getId() . '_orders.txt'));

            foreach ($filesToDelete as $file) {
                Storage::delete($file);
            }
            $setFileName = 'snapshots/' . $clientService->getId() . '_orders.gz';
            $latestFile = collect($files)->first(fn($file) => $file === $setFileName);
            if ($latestFile) {
                $gz = null;
                try {
                    $clientService->setUpdateInProgress(true);
                    $this->info('Processing snapshot for client service ID: ' . $clientService->getId());
                    $gz = gzopen(Storage::path($latestFile), 'rb');
                    $fileIndex = 1;
                    $lineCount = 0;
                    $buffer = '';
                    while (!gzeof($gz)) {
                        $line = gzgets($gz);
                        if (trim($line) === '') {
                            continue;
                        }
                        $buffer .= $line;
                        $lineCount++;
                        if ($lineCount % 2000 === 0) {
                            Storage::put("snapshots/{$fileIndex}_{$clientService->getId()}_orders.txt", $buffer);
                            $buffer = '';
                            $lineCount = 0;
                            $fileIndex++;
                        }
                    }

                    if ($buffer !== '') {
                        Storage::put("snapshots/{$fileIndex}_{$clientService->getId()}_orders.txt", $buffer);
                    }
                    $this->info('Snapshot processing complete for client service ID: ' . $clientService->getId());
                    $service = $clientService->service()->first();
                    $clientService->setQueueStatus($clientServiceStatus->next($service));
                    $clientService->save();
                } catch (Throwable $t) {
                    LoggerHelper::log($t->getMessage());
                    $this->error('Error updating orders for client service ID ' . $clientService->getId() . ': ' . $t->getMessage());
                    $success = false;
                } finally {
                    if ($gz) {
                        gzclose($gz);
                    }
                    Storage::delete($latestFile);
                    $clientService->setUpdateInProgress(false);
                }
            }
        }
        return $success ? Command::SUCCESS : Command::FAILURE;
    }
}
