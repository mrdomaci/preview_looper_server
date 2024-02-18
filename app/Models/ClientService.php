<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClientServiceStatusEnum;
use App\Enums\SyncEnum;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataUpdateFailException;
use App\Helpers\NumbersHelper;
use DateTime;
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

    /** @var array <string, string> */
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

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function getServiceId(): int
    {
        return $this->getAttribute('service_id');
    }

    public function getOAuthAccessToken(): string
    {
        return $this->getAttribute('oauth_access_token');
    }

    public function getStatus(): ClientServiceStatusEnum
    {
        return $this->getAttribute('status');
    }

    public function getCountry(): ?string
    {
        return $this->getAttribute('country');
    }

    public function getAccessToken(): ?string
    {
        return $this->getAttribute('access_token');
    }

    public function getOrdersLastSyncedAt(): ?DateTime
    {
        if ($this->getAttribute('orders_last_synced_at') === null) {
            return null;
        }
        return new DateTime($this->getAttribute('orders_last_synced_at'));
    }

    public function getProductsLastSyncedAt(): ?DateTime
    {
        if ($this->getAttribute('products_last_synced_at') === null) {
            return null;
        }
        return new DateTime($this->getAttribute('products_last_synced_at'));
    }

    public function isUpdateInProcess(): bool
    {
        return NumbersHelper::intToBool($this->getAttribute('update_in_process'));
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

    public function setStatusDeleted(): void
    {
        $this->setAttribute('status', ClientServiceStatusEnum::DELETED);
        $this->save();
    }

    public function setStatusInactive(): void
    {
        $this->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
        $this->save();
    }

    public function setStatusActive(): void
    {
        $this->setAttribute('status', ClientServiceStatusEnum::ACTIVE);
        $this->save();
    }

    public static function updateOrCreate(Client $client, Service $service, string $oAuthAccessToken, string $country): ClientService
    {
        $clientService = ClientService::where('client_id', $client->getId())
            ->where('service_id', $service->getId())
            ->first();
        if ($clientService === null) {
            $clientService = new ClientService();
            $clientService->setAttribute('client_id', $client->getId());
            $clientService->setAttribute('service_id', $service->getId());
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
        $clientService = ClientService::where('client_id', $client->getId())
        ->where('service_id', $service->getId())
        ->first();
        if ($clientService === null) {
            throw new DataNotFoundException(new \Exception('ClientService not found for client ' . $client->getId() . ' and service ' . $service->getId()));
        }
        $clientService->setAttribute('status', $status);
        try {
            $clientService->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
    }
}
