<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\BSON\ObjectID;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'driver_id',
        'customer_id',
        'status',
        'steps',
        'completed_steps',
        'duration',
        'order_location_point',
    ];

}