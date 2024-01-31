<?php

namespace App\Models;

use App\Enums\ClientServiceStatusEnum;
use App\Enums\SyncEnum;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataUpdateFailException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class ClientService extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'client_id',
        'service_id',
        'oauth_access_token',
        'status',
        'access_token',
        'country',
    ];

    /**
     * @var array <string, string>
     */
    protected $casts = [
        'status' => ClientServiceStatusEnum::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function setUpdateInProgress(bool $updateInProgress, ?SyncEnum $sync = null): void
    {
        $this->setAttribute('update_in_process', $updateInProgress);
        if ($sync !== null && $sync->isOrder()) {
            $this->setAttribute('orders_last_synced_at', now());
        }
        if ($sync !== null && $sync->isProduct()) {
            $this->setAttribute('products_last_synced_at', now());
        }
        try {
            $this->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
    }

    public function setStatusDeleted(): void {
        $this->setAttribute('status', ClientServiceStatusEnum::DELETED);
        $this->save();
    }

    public function setStatusInactive(): void {
        $this->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
        $this->save();
    }

    public function setStatusActive(): void {
        $this->setAttribute('status', ClientServiceStatusEnum::ACTIVE);
        $this->save();
    }

    public static function updateOrCreate(Client $client, Service $service, string $oAuthAccessToken, string $country): ClientService
    {
        $clientService = ClientService::where('client_id', $client->getAttribute('id'))
            ->where('service_id', $service->getAttribute('id'))
            ->first();
        if ($clientService === null) {
            $clientService = new ClientService();
            $clientService->setAttribute('client_id', $client->getAttribute('id'));
            $clientService->setAttribute('service_id', $service->getAttribute('id'));
        }
        $clientService->setAttribute('oauth_access_token', $oAuthAccessToken);
        $clientService->setAttribute('status', 'active');
        $clientService->setAttribute('country', $country);
        $clientService->setAttribute('update_in_process', false);
        $clientService->save();
        return $clientService;
    }

    public static function updateStatus(Client $client, Service $service, ClientServiceStatusEnum $status): void
    {
        $clientService = ClientService::where('client_id', $client->getAttribute('id'))
        ->where('service_id', $service->getAttribute('id'))
        ->first();
        if ($clientService === null) {
            throw new DataNotFoundException(new \Exception('ClientService not found for client ' . $client->getAttribute('id') . ' and service ' . $service->getAttribute('id')));
        }
        $clientService->setAttribute('status', $status);
        try {
            $clientService->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
    }
}
