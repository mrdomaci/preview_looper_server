<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\WebhookException;
use Exception;
use Nette\Utils\Json;

class WebHookHelper
{
    public const EVENT_UNINSTALL = 'addon:uninstall';
    public const EVENT_DEACTIVATE = 'addon:suspend';
    public const EVENT_ACTIVATE = 'addon:approve';

    public static function getEshopId(string $eventName): int
    {
        $body = file_get_contents('php://input');
        $webhook = Json::decode($body);
        if (ArrayHelper::containsKey($webhook, 'event') === false) {
            throw new WebhookException(new Exception('Webhook failed for event: ' . $eventName . ' - missing event key'));
        }
        $event = $webhook['event'];
        if ($event !== $eventName) {
            throw new WebhookException(new Exception('Webhook failed for event does not match: ' . $eventName . ' !== ' . $event));
        }
        if (ArrayHelper::containsKey($webhook, 'eshopId') === false) {
            throw new WebhookException(new Exception('Webhook failed for event: ' . $eventName . ' - missing eshopId key'));
        }
        return (int)$webhook['eshopId'];
    }
}