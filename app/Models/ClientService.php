<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Enums\ClientServiceStatusEnum;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'queue_status' => ClientServiceQueueStatusEnum::class,
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
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

    public function setUpdateInProgress(bool $updateInProgress): void
    {
        $this->setAttribute('update_in_process', $updateInProgress);
        $this->save();
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

    public function getCreatedAt(): DateTime
    {
        return $this->getAttribute('created_at');
    }

    public function getVariableSymbol(): string
    {
        return $this->created_at->format('y') . $this->service_id . str_pad((string)$this->getAttribute('id'), 6, '0', STR_PAD_LEFT);
    }

    public function getSyncedAt(): ?DateTime
    {
        if ($this->getAttribute('synced_at') === null) {
            return null;
        }
        return new DateTime($this->getAttribute('synced_at'));
    }

    public function setSyncedAt(?DateTime $syncedAt): self
    {
        return $this->setAttribute('synced_at', $syncedAt);
    }

    public function isLicenseActive(): bool
    {
        return (bool) $this->getAttribute('is_license_active');
    }

    public function setLicenseActive(bool $is_license_active): void
    {
        $this->setAttribute('is_license_active', $is_license_active);
        $this->save();
    }

    public function setWebhookedAt(?DateTime $webhoodAt): self
    {
        return $this->setAttribute('webhooked_at', $webhoodAt);
    }

    public function getWebhoodAt(): ?DateTime
    {
        if ($this->getAttribute('webhooked_at') === null) {
            return null;
        }
        return new DateTime($this->getAttribute('webhooked_at'));
    }

    public function getRenewalDate(): DateTime
    {
        $createdAt = $this->getCreatedAt();
        $renewalDate = clone $createdAt;
        while ($renewalDate <= new DateTime()) {
            $renewalDate->modify('+1 month');
        }
    
        return $renewalDate;
    }

    public function getQueueStatus(): ClientServiceQueueStatusEnum
    {
        return $this->getAttribute('queue_status');
    }

    public function setQueueStatus(ClientServiceQueueStatusEnum $queueStatus): self
    {
        if ($queueStatus->isDone()) {
            $this->setSyncedAt(new DateTime());
        }
        return $this->setAttribute('queue_status', $queueStatus);
    }
}
