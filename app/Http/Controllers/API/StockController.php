<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStockRequest;
use App\Models\Stock;
use App\Services\StockService;
use Exception;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function index(Request $request)
    {
        try {
            $data = $this->stockService->getAll($request->all());

            return ResponseFormatter::success($data, 'Data stok retrieved');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function show(string $id)
    {
        try {
            $stock = Stock::find($id);

            if (!$stock) {
                return ResponseFormatter::error('Stok not found', 404);
            }

             return ResponseFormatter::success($stock, 'Stok retrieved');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function store(CreateStockRequest $request)
    {
        try {
            $stock = $this->stockService->create($request->validated());

            return ResponseFormatter::success($stock, 'Stok created', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(CreateStockRequest $request, string $id)
    {
        try {
            $stock = Stock::find($id);

            if (!$stock) {
                return ResponseFormatter::error('Stok not found', 404);
            }

            $updated = $this->stockService->update($stock, $request->validated());

            return ResponseFormatter::success($updated, 'Stok updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function check(Request $request)
    {
        try {
            $request->validate([
                'item_name' => 'required|string',
                'quantity'  => 'required|integer|min:1',
            ]);

            $result = $this->stockService->checkAndDeduct($request->item_name, $request->quantity);

            return ResponseFormatter::success($result, 'check stock done');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
