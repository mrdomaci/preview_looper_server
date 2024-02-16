<?php

namespace App\Models;

use App\Exceptions\DataInsertFailException;
use App\Exceptions\DataUpdateFailException;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

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

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getEshopId(): int
    {
        return $this->getAttribute('eshop_id');
    }

    public function getEshopName(): string
    {
        return $this->getAttribute('eshop_name');
    }

    public function getEshopCategory(): string
    {
        return $this->getAttribute('eshop_category');
    }

    public function getEshopSubtitle(): string
    {
        return $this->getAttribute('eshop_subtitle');
    }

    public function getContactPerson(): string
    {
        return $this->getAttribute('contact_person');
    }

    public function getUrl(): ?string
    {
        return $this->getAttribute('url');
    }

    public function getEmail(): ?string
    {
        return $this->getAttribute('email');
    }

    public function getPhone(): string
    {
        return $this->getAttribute('phone');
    }

    public function getStreet(): string
    {
        return $this->getAttribute('street');
    }

    public function getCity(): string
    {
        return $this->getAttribute('city');
    }

    public function getZip(): string
    {
        return $this->getAttribute('zip');
    }

    public function getCountry(): string
    {
        return $this->getAttribute('country');
    }

    public function getLastSyncedAt(): DateTime
    {
        return new DateTime($this->getAttribute('last_synced_at'));
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
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

    public function productCategoryRecommendations(): Collection
    {
        return $this->hasMany(ProductCategoryRecommendation::class)->get();
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

    public static function updateOrCreate(int $eshopId, string $eshopUrl, string $email): Client
    {
        $client = Client::where('eshop_id', $eshopId)->first();

        if ($client === NULL) {
            try {
                $client = Client::create([
                    'eshop_id' => $eshopId,
                    'url' => $eshopUrl,
                    'email' => $email,
                ]);
            } catch (Throwable $t) {
                throw new DataInsertFailException($t);
            }
        } else {
            $client->url = $eshopUrl;
            $client->email = $email;
            try {
                $client->save();
            } catch (Throwable $t) {
                throw new DataUpdateFailException($t);
            }
        }
        return $client;
    }
}
