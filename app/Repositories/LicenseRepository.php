<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\DataNotFoundException;
use App\Models\ClientService;
use App\Models\License;
use Carbon\Carbon;
use Exception;
use Throwable;

class LicenseRepository
{

    public function get(int $id): License
    {
        $client = License::find($id);
        if ($client === null) {
            throw new DataNotFoundException(new Exception('License not found id: ' . $id));
        }
        return $client;
    }

    public function updateOrCreate(
        string $foreignId,
        ClientService $clientService,
        float $value,
        string $currency,
        string $accountNumber,
        string $bankCode,
        ?string $variableSymbol,
        ?string $specificSymbol,
        ?string $constantSymbol,
        ?string $note,
    ): License {
        try {
            $license = $this->getByForeignId($foreignId);
        } catch (Throwable) {
            $license = new License();
            $license->foreign_id = $foreignId;
        }

        $validTo = Carbon::now();
        $isActive = false;

        if ($currency === 'CZK') {
            if ($value > 4489) {
                $validTo = Carbon::now()->addDays(365);
                $isActive = true;
            } else if ($value > 489) {
                $validTo = Carbon::now()->addDays(31);
                $isActive = true;
            }
        }

        $license->client_service_id = $clientService->id;
        $license->value = $value;
        $license->currency = $currency;
        $license->valid_to = $validTo;
        $license->is_active = $isActive;
        $license->account_number = $accountNumber;
        $license->bank_code = $bankCode;
        $license->variable_symbol = $variableSymbol;
        $license->specific_symbol = $specificSymbol;
        $license->constant_symbol = $constantSymbol;
        $license->note = $note;

        $license->save();

        return $license;
    }

    public function getByForeignId(string $foreignId): License
    {
        $client = License::where('foreign_id', $foreignId)->first();
        if ($client === null) {
            throw new DataNotFoundException(new Exception('License not found foreignId: ' . $foreignId));
        }
        return $client;
    }

    public function getValidByClientService(ClientService $clientService): ?License
    {
        return License::where('client_service_id', $clientService->id)
            ->where('is_active', true)
            ->where('valid_to', '>', Carbon::now())
            ->first();
    }
}
