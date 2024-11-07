<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enums\EmailStatusEnum;
use App\Models\ClientService;
use App\Models\Email;

class EmailHelper
{
    public static function licenseEasyUpsell(ClientService $clientService): Email
    {
        $client = $clientService->client()->first();
        $email = new Email(
            [
                'recipient' => $client->getEmail(),
                'subject' => trans('easy-upsell.license_offer_subject'),
                'body' => view('emails.license_offer', ['clientService' => $clientService])->render(),
                'status' => EmailStatusEnum::NEW->name,
                'client_service_id' => $clientService->getId(),
            ]
        );
        $email->save();
        return $email;
    }
}
