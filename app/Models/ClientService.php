<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClientServiceStatusEnum;
use App\Enums\SyncEnum;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\DataUpdateFailException;
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

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }

    public function getServiceId(): int
    {
        return $this->getAttribute('service_id');
    }

    public function setService(Service $service): self
    {
        return $this->setAttribute('service_id', $service->getId());
    }

    public function getOAuthAccessToken(): string
    {
        return $this->getAttribute('oauth_access_token');
    }

    public function setOAuthAccessToken(string $oAuthAccessToken): self
    {
        return $this->setAttribute('oauth_access_token', $oAuthAccessToken);
    }

    public function getStatus(): ClientServiceStatusEnum
    {
        return $this->getAttribute('status');
    }

    public function setStatus(ClientServiceStatusEnum $status): self
    {
        return $this->setAttribute('status', $status);
    }

    public function getCountry(): ?string
    {
        return $this->getAttribute('country');
    }

    public function setCountry(?string $country): self
    {
        return $this->setAttribute('country', $country);
    }

    public function getAccessToken(): ?string
    {
        return $this->getAttribute('access_token');
    }

    public function setAccessToken(?string $accessToken): self
    {
        return $this->setAttribute('access_token', $accessToken);
    }

    public function getOrdersLastSyncedAt(): ?DateTime
    {
        if ($this->getAttribute('orders_last_synced_at') === null) {
            return null;
        }
        return new DateTime($this->getAttribute('orders_last_synced_at'));
    }

    public function setOrdersLastSyncedAt(?DateTime $ordersLastSyncedAt): self
    {
        return $this->setAttribute('orders_last_synced_at', $ordersLastSyncedAt);
    }

    public function getProductsLastSyncedAt(): ?DateTime
    {
        if ($this->getAttribute('products_last_synced_at') === null) {
            return null;
        }
        return new DateTime($this->getAttribute('products_last_synced_at'));
    }

    public function setProductsLastSyncedAt(?DateTime $productsLastSyncedAt): self
    {
        return $this->setAttribute('products_last_synced_at', $productsLastSyncedAt);
    }

    public function isUpdateInProcess(): bool
    {
        return (bool) $this->getAttribute('update_in_process');
    }

    public function getLastSyncedAt(): ?DateTime
    {
        if ($this->service()->first()->isUpsell()) {
            return $this->getOrdersLastSyncedAt();
        }
        if ($this->service()->first()->isDynamicPreviewImages()) {
            return $this->getProductsLastSyncedAt();
        }
        return null;
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
