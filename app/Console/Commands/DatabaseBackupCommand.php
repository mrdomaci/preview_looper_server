<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\BackupDBHelper;
use App\Helpers\DropBoxUploadHelper;
use DateTime;
use Illuminate\Console\Command;
use Throwable;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to a file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $path = 'storage/app/backup';
            $fileName = (new DateTime())->format('Y-m-d') . '_backup.sql';
            BackupDBHelper::run($path, $fileName);
            DropBoxUploadHelper::upload($path, $fileName);
            return Command::SUCCESS;
        } catch (Throwable $t) {
            $this->error($t->getMessage());
            return Command::FAILURE;
        }
    }
}
