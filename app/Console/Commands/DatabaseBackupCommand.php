<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\BackupDB;
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
            BackupDB::run();
            return Command::SUCCESS;
        } catch (Throwable $t) {
            $this->error($t->getMessage());
            return Command::FAILURE;
        }
    }
}
