<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{

    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'is_active',
        'category',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // relationship p.order
    public function procurementOrders()
    {
        return $this->hasMany(ProcurementOrder::class);
    }
}
