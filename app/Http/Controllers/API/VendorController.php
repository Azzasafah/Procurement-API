<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVendorRequest;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query()->where('is_active', true);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return ResponseFormatter::success(
            $query->paginate($request->input('limit', 10)),
            'Data vendor retrieved.'
        );
    }

    public function show(string $id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return ResponseFormatter::error('Vendor not found', 404);
        }

        return ResponseFormatter::success($vendor, 'Vendor found.');
    }

    public function store(CreateVendorRequest $request)
    {
        try {
            $vendor = Vendor::create(array_merge($request->validated(), ['is_active' => true]));
            return ResponseFormatter::success($vendor, 'Vendor created', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(CreateVendorRequest $request, string $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return ResponseFormatter::error('Vendor not found', 404);
            }

            $vendor->update($request->validated());

            return ResponseFormatter::success($vendor->fresh(), 'Vendor updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $vendor = Vendor::find($id);

            if (!$vendor) {
                return ResponseFormatter::error('Vendor not found', 404);
            }

            $vendor->delete();

            return ResponseFormatter::success(null, 'Vendor deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
