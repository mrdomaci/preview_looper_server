<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\EmailStatusEnum;
use App\Models\Email;
use Illuminate\Console\Command;
use Throwable;

class EmailSendCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $success = true;
        $emails = Email::where('status', EmailStatusEnum::NEW->name)
                    ->limit(10)->orderBy('id', 'ASC')->get();
        foreach ($emails as $email) {
            try {
                $email->send();
            } catch (Throwable $e) {
                $success = false;
                $this->error($e->getMessage());
            }
        }
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
