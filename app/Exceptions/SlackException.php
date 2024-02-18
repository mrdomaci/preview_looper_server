<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;
use Maknz\Slack\Client;
use Maknz\Slack\Message;

class SlackException extends \Exception
{
    public function __construct(Throwable $t)
    {
        $this->report($t);
    }
    public function report(Throwable $t): Message
    {
        $attachments = [];
        foreach ($t->getTrace() as $trace) {
            $attachments[] = [
                'text'      => $trace['file'] . ':' . $trace['line'],
                'color'     => 'danger',
            ];
        }
        $client = new Client($_ENV['SLACK_WEBHOOK_URL']);
        $client->to('#preview-looper');
        $client->from('Looper');
        foreach ($attachments as $attachment) {
            $client->attach($attachment);
        }
        return $client->send(get_class($t) . ': ' . $t->getMessage());
    }
}
