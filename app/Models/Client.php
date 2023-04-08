<?php

namespace App\Models;

use App\Enums\ClientStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'status' => ClientStatusEnum::class,
    ];

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
