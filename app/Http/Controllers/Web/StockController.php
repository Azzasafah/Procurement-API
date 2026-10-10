<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(protected StockService $stockService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['item_name', 'category']);
        $filters['limit'] = 10;

        $stocks = $this->stockService->getAll($filters);

        return view('stocks.index', compact('stocks'));
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name'     => 'required|string|max:255',
            'category'      => 'required|string|max:100',
            'quantity'      => 'required|integer|min:0',
            'unit'          => 'required|string|max:50',
            'location'      => 'nullable|string|max:100',
            'minimum_stock' => 'nullable|integer|min:0',
        ]);

        $this->stockService->create($request->all());

        return redirect()->route('web.stocks.index')
            ->with('success', 'Data stok barang baru berhasil didaftarkan ke gudang.');
    }

    public function check(Request $request)
    {
        $request->validate([
            'item_name'    => 'required|string',
            'required_qty' => 'required|integer|min:1',
        ]);

        $result = $this->stockService->checkAndDeduct(
            $request->input('item_name'),
            (int) $request->input('required_qty')
        );

        if ($result['available']) {
            return back()->with('stock_check_result', [
                'success' => true,
                'message' => "Stok untuk '{$result['item_name']}' TERSEDIA dan berhasil dipotong {$result['quantity_requested']} unit. Sisa stok di gudang saat ini: {$result['quantity_in_stock']} unit."
            ]);
        }

        return back()->with('stock_check_result', [
            'success' => false,
            'message' => "Stok untuk '{$result['item_name']}' TIDAK MENCUKUPI atau tidak ditemukan di gudang."
        ]);
    }
}
