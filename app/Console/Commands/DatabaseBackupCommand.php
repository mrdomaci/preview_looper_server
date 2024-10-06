<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Exceptions\DropboxFailException;
use App\Helpers\BackupDBHelper;
use App\Helpers\DropBoxUploadHelper;
use DateTime;
use Exception;
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
            $result = DropBoxUploadHelper::upload($path, $fileName);
            $this->info($result);
            $result = json_decode($result, true);
            if (isset($result['error'])) {
                throw new DropboxFailException(new Exception($result['error']));
            }
            return Command::SUCCESS;
        } catch (Throwable $t) {
            $this->error($t->getMessage());
            return Command::FAILURE;
        }
    }
}
