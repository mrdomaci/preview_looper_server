<?php

namespace App\Models;

use App\Enums\ClientStatusEnum;
use App\Exceptions\DataInsertFailException;
use App\Exceptions\DataUpdateFailException;
use App\Helpers\TokenHelper;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'oauth_access_token',
        'eshop_id',
        'eshop_name',
        'eshop_category',
        'eshop_subtitle',
        'constact_person',
        'url',
        'email',
        'phone',
        'street',
        'city',
        'zip',
        'country',
        'status',
        'last_synced_at',
        'settings_infinite_repeat',
        'settings_return_to_default',
        'settings_show_time',
        'access_token'
    ];

    /**
     * @var array <string, string>
     */
    protected $casts = [
        'status' => ClientStatusEnum::class,
    ];

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public static function getByEshopId(int $eshopId): Client
    {
        $client = Client::where('eshop_id', $eshopId)->first();
        if ($client === NULL) {
            throw new DataUpdateFailException(new Exception('Client not found'));
        }
        return $client;
    }

    public function getAccessToken(): string
    {
        return TokenHelper::getApiAccessToken($this);
    }
    public static function updateOrCreate(int $eshopId, string $oAuthAccessToken, string $eshopUrl, string $email): Client
    {
        $client = Client::where('eshop_id', $eshopId)->first();

        if ($client === NULL) {
            try {
                $client = Client::create([
                    'oauth_access_token' => $oAuthAccessToken,
                    'eshop_id' => $eshopId,
                    'url' => $eshopUrl,
                    'email' => $email,
                    'status' => ClientStatusEnum::ACTIVE,
                ]);
            } catch (Throwable $t) {
                throw new DataInsertFailException($t);
            }
        } else {
            $client->oauth_access_token = $oAuthAccessToken;
            $client->url = $eshopUrl;
            $client->email = $email;
            $client->status = ClientStatusEnum::ACTIVE;
            try {
                $client->save();
            } catch (Throwable $t) {
                throw new DataUpdateFailException($t);
            }
        }
        return $client;
    }

    public static function updateStatus(int $eshopId, ClientStatusEnum $status): void
    {
        $client = Client::getByEshopId($eshopId);
        $client->status = $status;
        try {
            $client->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
    }

    public static function updateSettings(Client $client, bool $infiniteRepeat, bool $returnToDefault, int $showTime): void
    {
        $client->settings_infinite_repeat = $infiniteRepeat;
        $client->settings_return_to_default = $returnToDefault;
        $client->settings_show_time = $showTime;
        try {
            $client->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
    }
}
