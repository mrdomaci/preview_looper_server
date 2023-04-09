<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'product_id',
        'url',
    ];


    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
