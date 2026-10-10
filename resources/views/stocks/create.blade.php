@extends('layouts.app')

@section('title', 'Tambah Master Stok Gudang | Sistem ERP')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.stocks.index') }}" class="hover:underline">Stok & Gudang</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold">Tambah Item</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Registrasi Master Stok Material</h1>
        </div>
        <a href="{{ route('web.stocks.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
            <span>&larr;</span>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
        <form action="{{ route('web.stocks.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="item_name" class="block text-xs font-semibold text-slate-800 mb-1.5">
                    Nama Barang / Material: <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="item_name" name="item_name" value="{{ old('item_name') }}" required placeholder="Contoh: Lakban Coklat 2 Inch" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Kategori Barang: <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="category" name="category" value="{{ old('category', 'Office Supplies') }}" required placeholder="Office Supplies" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                </div>

                <div>
                    <label for="unit" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Satuan Ukuran: <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', 'pcs') }}" required placeholder="pcs / rim / unit" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="quantity" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Jumlah Fisik Awal: <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" min="0" value="{{ old('quantity', 50) }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs">
                </div>

                <div>
                    <label for="minimum_stock" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Ambang Kritis Min.:
                    </label>
                    <input type="number" id="minimum_stock" name="minimum_stock" min="0" value="{{ old('minimum_stock', 10) }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs">
                </div>

                <div>
                    <label for="unit_price" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Harga Standar (Rp):
                    </label>
                    <input type="number" id="unit_price" name="unit_price" min="0" step="1000" value="{{ old('unit_price', 15000) }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-500">Stok baru langsung masuk ke data katalog gudang aktif.</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('web.stocks.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Master Stok</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
