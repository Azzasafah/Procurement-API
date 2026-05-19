<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'request_id',
        'approver_id',
        'status',
        'notes',
        'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime'
    ];

    // status state
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // relasi request
    public function request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    // relasi user
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
