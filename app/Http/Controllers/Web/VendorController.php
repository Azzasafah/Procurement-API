<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%');
            });
        }

        $vendors = $query->latest()->paginate(10);

        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:vendors,email',
            'phone'          => 'nullable|string|max:50',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
        ]);

        $code = $request->code ?: ('VND-' . strtoupper(\Illuminate\Support\Str::random(6)));

        $vendor = Vendor::create([
            'name'           => $request->name,
            'code'           => $code,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'is_active'      => true,
        ]);

        return redirect()->route('web.vendors.index')
            ->with('success', "Mitra vendor '{$vendor->name}' berhasil didaftarkan.");
    }

    public function show(string $id)
    {
        $vendor = Vendor::with(['procurementOrders.request'])->findOrFail($id);
        return view('vendors.show', compact('vendor'));
    }
}
