<?php

namespace App\Services;

use App\Exceptions\BussinessException;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcureService
{
    public function __construct(protected RequestService $requestservice) {}

    public function getAll(User $actor, array $filters)
    {
        $query = ProcurementOrder::with(['request', 'vendor', 'createdBy']);

        if (!$actor->hasAnyRole([User::ROLE_ADMIN, USER::ROLE_PURCHASING])) {
            throw new BussinessException('Unauthorized', 403);
        }

        if (!empty($filters['status'])) {
            $query->where('status', strtolower($filters['status']));
        }

        if (!empty($filters['vendor_id'])) {
            $query->where('vendor_id', $filters['vendor_id']);
        }

        if (!empty($filters['po_number'])) {
            $query->where('po_number', $filters['po_number']);
        }

        return $query->latest()->paginate($filters['limit'] ?? 10);
    }

    public function findOrFail(string $id)
    {
        $request = ProcurementOrder::with([
            'request',
            'vendor',
            'createdBy'
        ])->find($id);

        if (!$request) {
            throw new BussinessException('Request not found', 404);
        }

        return $request;
    }

    public function deliver(string $id, User $actor, Request $httpRequest)
    {
        return DB::transaction(function () use ($id, $actor, $httpRequest) {
            $po = ProcurementOrder::with('request')->lockForUpdate()->findOrFail($id);

            if (!$po->canTransitionTo(ProcurementOrder::STATUS_DELIVERED)) {
                throw new BussinessException("Cannot deliver. Current status: {$po->status}");
            }

            $po->update([
                'status' => ProcurementOrder::STATUS_DELIVERED,
                'delivered_at' => now()
            ]);

            $this->requestservice->complete($po->request_id, $actor, $httpRequest);

            return $po->fresh()->load(['vendor', 'request']);
        });
    }
}
