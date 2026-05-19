<?php

namespace App\Services;

use App\Exceptions\BussinessException;
use App\Helpers\GeneratePoNumber;
use App\Helpers\GenerateReqNumber;
use App\Models\Approval;
use App\Models\ProcurementRequest;
use App\Models\StatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestService
{
    /*
    * Ambil daftar request dengan filter opsional
    * Employee hanya bisa lihat miliknya sendiri
    */
    public function getAll(User $actor, array $filters)
    {
        $query = ProcurementRequest::with(['requester', 'department', 'items', 'approvals.approver']);

        /* 
        * kalau yang login employee 
        * dia cuma boleh lihat request miliknya sendiri
        */
        if ($actor->hasRole(User::ROLE_EMPLOYEE)) {
            $query->where('requester_id', $actor->id);
        }

        if (!empty($filters['status'])) {
            $query->where('status', strtoupper($filters['status']));
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['requester_id'])) {
            $query->where('requester_id', $filters['requester_id']);
        }

        if (!empty($filters['request_number'])) {
            $query->where('request_number', $filters['request_number']);
        }

        return $query->orderByDesc('created_at')->paginate($filters['limit'] ?? 10);
    }

    /*
    * Ambil satu request by ID beserta relasi
    * Employee hanya bisa lihat miliknya sendiri
    */
    public function findOrFail(string $id)
    {
        $request = ProcurementRequest::with([
            'requester',
            'department',
            'items',
            'approvals.approver',
            'statusHistories.actor',
            'procurementOrders.vendor'
        ])->find($id);

        if (!$request) {
            throw new BussinessException('Request not found', 404);
        }

        return $request;
    }

    /*
    * create request (saat status draft)
    * 
    * @throws BusinessException
    */
    public function create(User $actor, array $data, Request $httpRequest)
    {
        return DB::transaction(function () use ($actor, $data, $httpRequest) {
            $procurementRequest = ProcurementRequest::create([
                'request_number' => GenerateReqNumber::generate(),
                'requester_id'   => $actor->id,
                'department_id'  => $actor->department_id,
                'status'         => ProcurementRequest::STATUS_DRAFT,
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $procurementRequest->items()->create([
                    'item_name'       => $item['item_name'],
                    'category'        => $item['category'],
                    'quantity'        => $item['quantity'],
                    'unit'            => $item['unit'],
                    'estimated_price' => $item['estimated_price'] ?? null,
                    'notes'           => $item['notes'] ?? null,
                ]);
            }

            $this->recordHistory($procurementRequest, $actor, null, ProcurementRequest::STATUS_DRAFT, 'Request created', $httpRequest);

            return $procurementRequest->load(['requester', 'department', 'items']);
        });
    }

    /*
    * Update catatan request (saat status draft)
    * 
    * @throws BusinessException
    */
    public function update(ProcurementRequest $procurementRequest, User $actor, array $data)
    {
        /* 
        * memastikan bahwa requester merupakan actor yang sama (milik perequest sendiri)
        */
        if ($procurementRequest->requester_id !== $actor->id) {
            throw new BussinessException('Unauthorized to update this request', 403);
        }

        // cek bahwa status tetap draft
        if ($procurementRequest->status !== ProcurementRequest::STATUS_DRAFT) {
            throw new BussinessException('Only draft can be updated');
        }

        $procurementRequest->update(['notes' => $data['notes'] ?? $procurementRequest->notes]);

        return $procurementRequest->load(['requester', 'department', 'items']);
    }


    /*
    * Submit request: DRAFT => SUBMITTED
    * 
    * @throws BusinessException
    */
    public function submit(string $id, User $actor, Request $httpRequest)
    {
        return DB::transaction(function () use ($id, $actor, $httpRequest) {
            $procurementRequest = ProcurementRequest::lockForUpdate()->findOrFail($id);

            if ($procurementRequest->requester_id !== $actor->id) {
                throw new BussinessException('Unauthorized', 403);
            }

            if ($procurementRequest->items()->count() === 0) {
                throw new BussinessException('Request must have at least one item');
            }

            $from = $procurementRequest->status;

            $procurementRequest->update([
                'status'       => ProcurementRequest::STATUS_SUBMITTED,
                'submitted_at' => now()
            ]);

            $this->recordHistory($procurementRequest, $actor, $from, procurementRequest::STATUS_SUBMITTED, 'Request submitted for review', $httpRequest);

            return $procurementRequest->load(['requester', 'department', 'items']);
        });
    }

    /*
    * Submit request: Submitted => Approved
    * 
    * @throws BusinessException
    */
    public function approve(string $id, User $actor, ?string $notes, Request $httpRequest)
    {
        return DB::transaction(function () use ($id, $actor, $notes, $httpRequest) {
            $procurementRequest = ProcurementRequest::lockForUpdate()->findOrFail($id);

            if (!$procurementRequest->canTransitionTo(ProcurementRequest::STATUS_APPROVED)) {
                throw new BussinessException("Cannot approve. Current status: {$procurementRequest->status}");
            }

            $alreadyApproved = $procurementRequest->approvals()
                ->where('approver_id', $actor->id)
                ->where('status', Approval::STATUS_APPROVED)
                ->exists();

            if ($alreadyApproved) {
                throw new BussinessException("You've already approved this request");
            }

            $from = $procurementRequest->status;

            $procurementRequest->approvals()->create([
                'approver_id' => $actor->id,
                'status'      => Approval::STATUS_APPROVED,
                'notes'       => $notes,
                'approved_at' => now()
            ]);

            $procurementRequest->update(['status' => ProcurementRequest::STATUS_APPROVED]);

            $this->recordHistory($procurementRequest, $actor, $from, ProcurementRequest::STATUS_APPROVED, $notes ?? 'Request approved', $httpRequest);

            return $procurementRequest->load(['requester', 'department', 'items', 'approvals.approver']);
        });
    }

    /*
    * Update request: Submitted => Rejected
    * 
    * @throws BusinessException
    */
    public function reject(string $id, User $actor, ?string $reason, Request $httpRequest)
    {
        return DB::transaction(function () use ($id, $actor, $reason, $httpRequest) {
            $procurementRequest = ProcurementRequest::lockForUpdate()->findOrFail($id);

            if (!$procurementRequest->canTransitionTo(ProcurementRequest::STATUS_REJECTED)) {
                throw new BussinessException("Cannot reject. Current status: {$procurementRequest->status}");
            }

            $from = $procurementRequest->status;

            $procurementRequest->approvals()->create([
                'approver_id' => $actor->id,
                'status'      => Approval::STATUS_REJECTED,
                'notes'       => $reason,
                'approver_at' => now()
            ]);

            $procurementRequest->update(['status' => ProcurementRequest::STATUS_REJECTED]);

            $this->recordHistory($procurementRequest, $actor, $from, ProcurementRequest::STATUS_REJECTED, $reason, $httpRequest);

            return $procurementRequest->load(['requester', 'department', 'items', 'approvals.approver']);
        });
    }

    /*
    * Update request: Approved => IN_PROCUREMENT
    * 
    * @throws BusinessException
    */
    public function procure(string $id, User $actor, array $data, Request $httpRequest)
    {
        return DB::transaction(function () use ($id, $actor, $data, $httpRequest) {
            $procurementRequest = ProcurementRequest::lockForUpdate()->findOrFail($id);

            if (!$procurementRequest->canTransitionTo(ProcurementRequest::STATUS_IN_PROCUREMENT)) {
                throw new BussinessException("Cannot procure. Current status: {$procurementRequest->status}");
            }

            $from = $procurementRequest->status;

            $po = $procurementRequest->procurementOrders()->create([
                'po_number'              => GeneratePoNumber::generate(),
                'vendor_id'              => $data['vendor_id'],
                'created_by'             => $actor->id,
                'status'                 => 'ordered',
                'total_amount'           => $data['total_amount'],
                'notes'                  => $data['notes'] ?? null,
                'expected_delivery_date' => $data['expected_delivery_date'],
            ]);

            $procurementRequest->update(['status' => ProcurementRequest::STATUS_IN_PROCUREMENT]);

            $this->recordHistory($procurementRequest, $actor, $from, ProcurementRequest::STATUS_IN_PROCUREMENT, "PO #{$po->po_number} created", $httpRequest);

            return [
                'request'           => $procurementRequest->load(['requester', 'items']),
                'procurement_order' => $po->load('vendor')
            ];
        });
    }

    /*
    * Update request: IN_PROCUREMENT => COMPLETED
    * 
    * @throws BusinessException
    */
    public function complete(string $id, User $actor, Request $httpRequest)
    {
        return DB::transaction(function () use ($id, $actor, $httpRequest) {
            $procurementRequest = ProcurementRequest::lockForUpdate()->findOrFail($id);

            if (!$procurementRequest->canTransitionTo(ProcurementRequest::STATUS_COMPLETED)) {
                throw new BussinessException("Cannot complete. Current status: {$procurementRequest->status}");
            }

            $po = $procurementRequest->procurementOrders()->firstOrFail();

            $from = $procurementRequest->status;

            $procurementRequest->update(['status' => ProcurementRequest::STATUS_COMPLETED]);

            $this->recordHistory($procurementRequest, $actor, $from, ProcurementRequest::STATUS_COMPLETED, "PO #{$po->po_number} completed", $httpRequest);

            return [
                'request'           => $procurementRequest->load(['requester', 'items']),
                'procurement_order' => $po->load('vendor')
            ];
        });
    }

    /*
     * Soft delete request (hanya DRAFT).
     *
     * @throws BusinessException
     */
    public function delete(ProcurementRequest $procurementRequest, User $actor)
    {
        if ($procurementRequest->requester_id !== $actor->id && !$actor->hasRole(User::ROLE_ADMIN)) {
            throw new BussinessException('Unauthorized', 403);
        }

        if ($procurementRequest->status !== ProcurementRequest::STATUS_DRAFT) {
            throw new BussinessException('Only DRAFT requests can be deleted.');
        }

        $procurementRequest->delete();
    }


    /*
    * Simpan update status ke audit trail (immutable)
    */
    private function recordHistory(
        ProcurementRequest $procurementRequest,
        User $actor,
        ?string $from,
        string $to,
        ?string $notes,
        Request $httpRequest
    ): void {
        StatusHistory::create([
            'request_id'  => $procurementRequest->id,
            'changed_by'  => $actor->id,
            'from_status' => $from,
            'to_status'   => $to,
            'notes'       => $notes,
            'ip_address'  => $httpRequest->ip(),
            'user_agent'  => $httpRequest->userAgent()
        ]);
    }
}
