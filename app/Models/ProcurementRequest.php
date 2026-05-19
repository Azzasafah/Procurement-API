<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementRequest extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'requests';

    protected $fillable = [
        'request_number',
        'requester_id',
        'department_id',
        'status',
        'notes',
        'submitted_at',
        'completed_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // status constanst - State Machine
    const STATUS_DRAFT          = 'DRAFT';
    const STATUS_SUBMITTED      = 'SUBMITTED';
    const STATUS_APPROVED       = 'APPROVED';
    const STATUS_REJECTED       = 'REJECTED';
    const STATUS_IN_PROCUREMENT = 'IN_PROCUREMENT';
    const STATUS_COMPLETED      = 'COMPLETED';

    const VALID_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_IN_PROCUREMENT,
        self::STATUS_COMPLETED,
    ];

    // state transtion yang diizinkan
    // mencegah invalid state transitions
    const ALLOWED_TRANSITIONS = [
        self::STATUS_DRAFT          => [self::STATUS_SUBMITTED],
        self::STATUS_SUBMITTED      => [self::STATUS_APPROVED, self::STATUS_REJECTED],
        self::STATUS_APPROVED       => [self::STATUS_IN_PROCUREMENT, self::STATUS_COMPLETED],
        self::STATUS_REJECTED       => [],
        self::STATUS_IN_PROCUREMENT => [self::STATUS_COMPLETED],
        self::STATUS_COMPLETED      => []
    ];

    // check apakah transition ke status baru diizinkan
    public function canTransitionTo(string $newStatus)
    {
        return in_array($newStatus, self::ALLOWED_TRANSITIONS[$this->status] ?? []);
    }

    // relationship user
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    // relationship department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // relationship request
    public function items()
    {
        return $this->hasMany(RequestItem::class, 'request_id');
    }

    // relationship approval
    public function approvals()
    {
        return $this->hasMany(Approval::class, 'request_id');
    }

    // relationship statushistory
    public function statusHistories()
    {
        return $this->hasMany(statusHistory::class, 'request_id');
    }

    // relationship procurementorder
    public function procurementOrders()
    {
        return $this->hasMany(ProcurementOrder::class, 'request_id');
    }
}
