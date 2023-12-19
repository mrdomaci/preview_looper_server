<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $filable = [
        'product_id',
        'primary_text',
        'secondary_text',
        'link',
        'image_url',
        'template',
        'producer',
        'date_show_from',
        'date_show_to',
    ];
}
