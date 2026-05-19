<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestItem extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'request_id',
        'item_name',
        'category',
        'quantity',
        'unit',
        'estimated_price',
        'notes',
        'stock_checked',
        'stock_available'
    ];

    protected $casts = [
        'quantity'        => 'integer',
        'estimated_price' => 'decimal:2',
        'stock_checked'   => 'boolean',
        'stock_available' => 'boolean'
    ];

    // relasi request
    public function request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }
}
