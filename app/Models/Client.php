<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'eshop_id',
        'eshop_name',
        'eshop_category',
        'eshop_subtitle',
        'contact_person',
        'url',
        'email',
        'phone',
        'street',
        'city',
        'zip',
        'country',
        'last_synced_at',
    ];

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getEshopId(): int
    {
        return $this->getAttribute('eshop_id');
    }

    public function setEshopId(int $eshopId): self
    {
        return $this->setAttribute('eshop_id', $eshopId);
    }

    public function getEshopName(): ?string
    {
        return $this->getAttribute('eshop_name');
    }
    
    public function setEshopName(?string $eshopName): self
    {
        return $this->setAttribute('eshop_name', $eshopName);
    }

    public function getEshopCategory(): ?string
    {
        return $this->getAttribute('eshop_category');
    }

    public function setEshopCategory(?string $eshopCategory): self
    {
        return $this->setAttribute('eshop_category', $eshopCategory);
    }

    public function getEshopSubtitle(): ?string
    {
        return $this->getAttribute('eshop_subtitle');
    }

    public function setEshopSubtitle(?string $eshopSubtitle): self
    {
        return $this->setAttribute('eshop_subtitle', $eshopSubtitle);
    }

    public function getContactPerson(): ?string
    {
        return $this->getAttribute('contact_person');
    }

    public function setContactPerson(?string $contactPerson): self
    {
        return $this->setAttribute('contact_person', $contactPerson);
    }

    public function getUrl(): string
    {
        return $this->getAttribute('url');
    }

    public function setUrl(string $url): self
    {
        return $this->setAttribute('url', $url);
    }

    public function getEmail(): string
    {
        return $this->getAttribute('email');
    }

    public function setEmail(string $email): self
    {
        return $this->setAttribute('email', $email);
    }

    public function getPhone(): ?string
    {
        return $this->getAttribute('phone');
    }

    public function setPhone(?string $phone): self
    {
        return $this->setAttribute('phone', $phone);
    }

    public function getStreet(): ?string
    {
        return $this->getAttribute('street');
    }

    public function setStreet(?string $street): self
    {
        return $this->setAttribute('street', $street);
    }

    public function getCity(): ?string
    {
        return $this->getAttribute('city');
    }

    public function setCity(?string $city): self
    {
        return $this->setAttribute('city', $city);
    }

    public function getZip(): ?string
    {
        return $this->getAttribute('zip');
    }

    public function setZip(?string $zip): self
    {
        return $this->setAttribute('zip', $zip);
    }

    public function getCountry(): string
    {
        return $this->getAttribute('country');
    }

    public function setCountry(?string $country): self
    {
        return $this->setAttribute('country', $country);
    }

    public function getLastSyncedAt(): DateTime
    {
        return new DateTime($this->getAttribute('last_synced_at'));
    }

    public function setLastSyncedAt(DateTime $lastSyncedAt): self
    {
        return $this->setAttribute('last_synced_at', $lastSyncedAt);
    }

    public function getCreatedAt(): DateTime
    {
        return $this->getAttribute('created_at');
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->getAttribute('updated_at');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ClientService::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function productCategoryRecommendations(): HasMany
    {
        return $this->hasMany(ProductCategoryRecommendation::class)
                    ->where('is_forbidden', false);
    }

    public function productForbiddenRecommendations(): HasMany
    {
        return $this->hasMany(ProductCategoryRecommendation::class)
                    ->where('is_forbidden', true);
    }

    public function ClientSettingsServiceOptions(): HasMany
    {
        return $this->hasMany(ClientSettingsServiceOption::class);
    }

    public function dynamicPreviewImages(): ?ClientService
    {
        foreach ($this->services()->get() as $service) {
            if ($service->service()->first()->isDynamicPreviewImages()) {
                return $service;
            }
        }
        return null;
    }

    public function upsell(): ?ClientService
    {
        foreach ($this->services()->get() as $service) {
            if ($service->service()->first()->isUpsell()) {
                return $service;
            }
        }
        return null;
    }
}
