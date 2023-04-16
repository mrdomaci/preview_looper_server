<?php
declare(strict_types=1);

namespace App\Helpers;

use Maknz\Slack\Client;
use Maknz\Slack\Message;

class LoggerHelper
{
    public static function log(string $message): Message
    {
        $client = new Client($_ENV['SLACK_WEBHOOK_URL']);
        $client->to('#preview-looper');
        $client->from('Looper');
        return $client->send($message);
    }
}