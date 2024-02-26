<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\WebhookException;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Nette\Utils\Json;

class WebHookHelper
{
    public const EVENT_UNINSTALL = 'addon:uninstall';
    public const EVENT_DEACTIVATE = 'addon:suspend';
    public const EVENT_ACTIVATE = 'addon:approve';
    private const JENKINS_TRIGGER_URL = 'http://slabihoud.cz:8080/generic-webhook-trigger/invoke?token=';

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

    public static function webhookResolver(ClientService $clientService): Response
    {
        $client = $clientService->client()->first();
        $service = $clientService->service()->first();
        if ($service->isDynamicPreviewImages()) {
            return self::jenkinsWebhookDynamicPreviewImages($client, $service);
        }
        if ($service->isUpsell()) {
            return self::jenkinsWebhookUpsell($client, $service);
        }
        throw new WebhookException(new Exception('Webhook failed for client: ' . $client->getId() . ' and service ' . $service->getId() . '.'));
    }

    private static function jenkinsWebhookDynamicPreviewImages(Client $client, Service $service): Response
    {
        $url = self::JENKINS_TRIGGER_URL . env('JENKINS_HASH_DYNAMIC_PREVIEW_IMAGES');
        return Http::post($url, ['client' => (string) $client->getId(), 'service' => (string) $service->getId()]);
    }

    private static function jenkinsWebhookUpsell(Client $client, Service $service): Response
    {
        $url = self::JENKINS_TRIGGER_URL . env('JENKINS_HASH_UPSELL');
        return Http::post($url, ['client' => (string) $client->getId(), 'service' => (string) $service->getId()]);
    }
}
