<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusHistory extends Model
{
    use HasFactory,HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    // Tidak ada updated_at karena record ini immutable
    const UPDATED_AT = null;

    protected $fillable = [
        'request_id',
        'changed_by',
        'from_status',
        'to_status',
        'notes',
        'ip_address',
        'user_agent',
    ];

    // Override delete - Status history tidak boleh dihapus
    public function delete()
    {
        throw new \Exception('StatusHistory records are immutable and cannot be deleted.');
    }

    // Override update - StatusHistory tidak boleh diubah.
    public function update(array $attributes = [], array $options = [])
    {
        throw new \Exception('StatusHistory records are immutable and cannot be updated.');
    }

    // relasi request
    public function request()
    {
        return $this->belongsTo(ProcurementRequest::class, 'request_id');
    }

    // relasi user
    public function actor()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
