<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ProcurementRequest;
use App\Models\Vendor;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function __construct(protected RequestService $requestService) {}

    public function index(Request $request)
    {
        $actor = Auth::user();
        $filters = $request->only(['status', 'request_number', 'department_id']);
        $filters['limit'] = 10;

        $requests = $this->requestService->getAll($actor, $filters);
        $departments = Department::orderBy('name')->get();

        return view('requests.index', compact('requests', 'departments'));
    }

    public function create()
    {
        $actor = Auth::user();
        return view('requests.create', compact('actor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.item_name'       => 'required|string|max:255',
            'items.*.category'        => 'required|string|max:100',
            'items.*.quantity'        => 'required|integer|min:1',
            'items.*.unit'            => 'required|string|max:50',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes'           => 'nullable|string|max:255',
        ]);

        try {
            $created = $this->requestService->create(Auth::user(), $request->all(), $request);
            return redirect()->route('web.requests.show', $created->id)
                ->with('success', 'Draf pengadaan berhasil dibuat dengan nomor ' . $created->request_number);
        } catch (BussinessException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan pengadaan.');
        }
    }

    public function show(string $id)
    {
        try {
            $procurementRequest = $this->requestService->findOrFail($id);
            $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
            return view('requests.show', compact('procurementRequest', 'vendors'));
        } catch (BussinessException $e) {
            return redirect()->route('web.requests.index')->with('error', $e->getMessage());
        }
    }

    public function submit(string $id, Request $httpRequest)
    {
        try {
            $this->requestService->submit($id, Auth::user(), $httpRequest);
            return back()->with('success', 'Pengadaan berhasil diajukan untuk ditinjau oleh Manajer.');
        } catch (BussinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(string $id, Request $httpRequest)
    {
        try {
            $this->requestService->approve($id, Auth::user(), $httpRequest->input('notes'), $httpRequest);
            return back()->with('success', 'Pengadaan berhasil disetujui (Approved). Siap diproses oleh tim Purchasing.');
        } catch (BussinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(string $id, Request $httpRequest)
    {
        $httpRequest->validate([
            'reason' => 'required|string|min:3',
        ], [
            'reason.required' => 'Alasan penolakan wajib dicantumkan.'
        ]);

        try {
            $this->requestService->reject($id, Auth::user(), $httpRequest->input('reason'), $httpRequest);
            return back()->with('success', 'Pengadaan telah ditolak (Rejected) dengan catatan tersimpan pada audit trail.');
        } catch (BussinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function procure(string $id, Request $httpRequest)
    {
        $httpRequest->validate([
            'vendor_id'              => 'required|exists:vendors,id',
            'expected_delivery_date' => 'required|date|after_or_equal:today',
            'total_amount'           => 'required|numeric|min:0',
            'notes'                  => 'nullable|string',
        ]);

        try {
            $result = $this->requestService->procure($id, Auth::user(), $httpRequest->all(), $httpRequest);
            return back()->with('success', 'Purchase Order berhasil diterbitkan dengan nomor ' . $result['procurement_order']->po_number);
        } catch (BussinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(string $id, Request $httpRequest)
    {
        try {
            $this->requestService->complete($id, Auth::user(), $httpRequest);
            return back()->with('success', 'Pengadaan telah selesai dipenuhi (Completed).');
        } catch (BussinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
