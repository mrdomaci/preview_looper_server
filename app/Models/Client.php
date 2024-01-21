<?php

namespace App\Models;

use App\Exceptions\DataInsertFailException;
use App\Exceptions\DataUpdateFailException;
use Exception;
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

    public static function getByEshopId(int $eshopId): Client
    {
        $client = Client::where('eshop_id', $eshopId)->first();
        if ($client === NULL) {
            throw new DataUpdateFailException(new Exception('Client not found'));
        }
        return $client;
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
