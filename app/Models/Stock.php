<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'item_name',
        'category',
        'quantity',
        'unit',
        'location',
        'minimum_stock',
        'request_item_id'
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'minimum_stock' => 'integer'
    ];

    // check apakah stock mencukupi
    // menggunakan pessimistic locking untuk race condtion
    public function isAvailable(int $requiredQuantity)
    {
        return $this->quantity >= $requiredQuantity;
    }
}
