<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Repositories\ClientServiceQueueRepository;
use Illuminate\Console\Command;

class PruneClientServiceQueueCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prune:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune client service queue';

    public function __construct(private readonly ClientServiceQueueRepository $clientServiceQueueRepository)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->clientServiceQueueRepository->prune();
        $this->info('Client service queue pruned');
        return Command::SUCCESS;
    }
}
