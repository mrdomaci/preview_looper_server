<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\QueueBusiness;
use App\Models\Queue;
use App\Repositories\QueueRepository;
use Illuminate\Console\Command;
use Throwable;

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
    protected $description = 'Download data from resolved API queue';

    public function __construct(
        private readonly QueueRepository $queueRepository,
        private readonly QueueBusiness $queueBusiness,
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
            try {
                $this->queueBusiness->download($queue);
            } catch (Throwable $e) {
                $success = false;
                $this->error('Queue download failed: ' . $queue->getId() . ' ' . $e->getMessage());
            }
        }

        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
