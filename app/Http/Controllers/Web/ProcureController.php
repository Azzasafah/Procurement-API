<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\BussinessException;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\ProcureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcureController extends Controller
{
    public function __construct(protected ProcureService $procureService) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'po_number', 'vendor_id']);
            $filters['limit'] = 10;

            $orders = $this->procureService->getAll(Auth::user(), $filters);
            $vendors = Vendor::where('is_active', true)->orderBy('name')->get();

            return view('procures.index', compact('orders', 'vendors'));
        } catch (BussinessException $e) {
            return redirect()->route('web.dashboard')->with('error', $e->getMessage());
        }
    }

    public function show(string $id)
    {
        try {
            $order = $this->procureService->findOrFail($id);
            return view('procures.show', compact('order'));
        } catch (BussinessException $e) {
            return redirect()->route('web.procures.index')->with('error', $e->getMessage());
        }
    }

    public function deliver(string $id, Request $httpRequest)
    {
        try {
            $this->procureService->deliver($id, Auth::user(), $httpRequest);
            return back()->with('success', 'Barang berhasil dikonfirmasi diterima (Delivered). Siklus pengadaan ditandai Selesai.');
        } catch (BussinessException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
