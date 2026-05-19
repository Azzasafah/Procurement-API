<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementOrder extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'po_number',
        'request_id',
        'vendor_id',
        'created_by',
        'status',
        'total_amount',
        'notes',
        'expected_delivery_date',
        'delivered_at'
    ];

    protected $casts = [
        'total_amount'           => 'integer',
        'expected_delivery_date' => 'date',
        'delivered_at'           => 'datetime',
    ];

    // status state
    const STATUS_PENDING   = 'pending';
    const STATUS_ORDERED   = 'ordered';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING        => [self::STATUS_ORDERED],
        self::STATUS_ORDERED        => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
        self::STATUS_DELIVERED      => [],
        self::STATUS_CANCELLED      => [],
    ];

    public function canTransitionTo(string $newStatus)
    {
        return in_array($newStatus, self::ALLOWED_TRANSITIONS[$this->status] ?? []);
    }

    // relasi request
    public function request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    // relasi vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
