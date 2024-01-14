<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'order_id',
        'order_guid',
        'product_id',
        'product_guid',
    ]; 
}
