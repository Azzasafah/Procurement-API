@extends('layouts.app')

@section('title', 'Katalog Stok Gudang | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Logistik & Gudang Fisik</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Inventaris & Stok Logistik Gudang</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Monitoring kuantitas fisik material secara real-time dengan proteksi mutasi konkurensi database (Pessimistic Locking) untuk mencegah alokasi ganda.
            </p>
        </div>

        @if(auth()->user()->hasRole('warehouse') || auth()->user()->hasRole('admin'))
            <a href="{{ route('web.stocks.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs flex items-center gap-2 self-start md:self-auto shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Tambah Master Stok</span>
            </a>
        @endif
    </div>

    <!-- Concurrency Lock Simulation Widget -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 text-2xs font-bold rounded-full bg-blue-50 text-blue-800 border border-blue-200 uppercase tracking-wide">Anti-Race Condition</span>
            <h2 class="text-sm font-bold text-slate-900">Simulasi Cek Ketersediaan Stok Real-time (lockForUpdate)</h2>
        </div>
        <p class="text-xs text-slate-500 leading-relaxed max-w-3xl">
            Menguji penguncian baris database saat beberapa operator memeriksa atau mengambil stok secara bersamaan. Database mengisolasi record dengan SQL <code class="px-1.5 py-0.5 rounded bg-slate-100 font-mono text-slate-800">SELECT ... FOR UPDATE</code> agar tidak terjadi alokasi ganda.
        </p>

        <form action="{{ route('web.stocks.check') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end pt-2">
            @csrf
            <div class="sm:col-span-5">
                <label for="check_item_name" class="block text-2xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nama Barang Material:</label>
                <input type="text" id="check_item_name" name="item_name" required placeholder="Contoh: Kertas A4" value="{{ old('item_name') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
            </div>
            <div class="sm:col-span-3">
                <label for="check_qty" class="block text-2xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Jumlah Dibutuhkan:</label>
                <input type="number" id="check_qty" name="required_qty" min="1" required value="{{ old('required_qty', 5) }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs">
            </div>
            <div class="sm:col-span-4">
                <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-sm transition">
                    Uji Ketersediaan Stok &rarr;
                </button>
            </div>
        </form>

        @if(session('stock_check_result'))
            @php $res = session('stock_check_result'); @endphp
            <div class="mt-4 p-4 rounded-xl text-xs {{ $res['success'] ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-rose-50 text-rose-900 border border-rose-200' }} shadow-2xs flex items-center justify-between">
                <div>
                    <strong>{{ $res['success'] ? '✓ Verifikasi Berhasil:' : '✕ Peringatan Stok:' }}</strong>
                    <span class="ml-1">{{ $res['message'] }}</span>
                </div>
            </div>
        @elseif(session('check_result'))
            @php $res = session('check_result'); @endphp
            <div class="mt-4 p-4 rounded-xl text-xs {{ $res['available'] ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-rose-50 text-rose-900 border border-rose-200' }} shadow-2xs">
                @if($res['available'])
                    <strong>✓ Stok Terverifikasi:</strong> Barang "{{ $res['item_name'] }}" tersedia. Penguncian baris database berhasil.
                @else
                    <strong>✕ Stok Tidak Mencukupi:</strong> Barang "{{ $res['item_name'] }}" tidak mencukupi. Pengadaan disarankan.
                @endif
            </div>
        @endif
    </div>

    <!-- Stocks Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Katalog Stok Material Fisik</h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono">{{ $stocks->total() }} Item Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nama Barang</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Kategori</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-center">Jumlah Stok</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Satuan</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Lokasi Rak</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-center">Batas Minimum</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status Ambang Batas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stocks as $stk)
                        @php
                            $isCritical = (int)$stk->quantity <= (int)$stk->minimum_stock;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $stk->item_name }}</td>
                            <td class="py-3.5 px-4 text-slate-600 font-medium">{{ $stk->category }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sm tabular-nums {{ $isCritical ? 'text-rose-600' : 'text-slate-900' }}">
                                {{ $stk->quantity }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $stk->unit }}</td>
                            <td class="py-3.5 px-4 text-slate-700 font-mono text-2xs">{{ $stk->location ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-500 tabular-nums">{{ $stk->minimum_stock }}</td>
                            <td class="py-3.5 px-4">
                                @if($isCritical)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-2xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        <span>Stok Kritis / Menipis</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-2xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Stok Aman</span>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Belum ada data stok material di gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
