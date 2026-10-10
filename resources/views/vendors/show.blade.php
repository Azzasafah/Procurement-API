@extends('layouts.app')

@section('title', 'Profil Vendor ' . $vendor->name . ' | Sistem ERP')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.vendors.index') }}" class="hover:underline">Rekanan & Vendor</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold">{{ $vendor->name }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $vendor->name }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-semibold {{ $vendor->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $vendor->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    <span>{{ $vendor->is_active ? 'Terverifikasi Aktif' : 'Non-aktif' }}</span>
                </span>
            </div>
        </div>
        <a href="{{ route('web.vendors.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
            <span>&larr;</span>
            <span>Kembali ke Katalog</span>
        </a>
    </div>

    <!-- Vendor Profile Cards -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Kode Rekanan</span>
            <div class="text-sm font-mono font-bold text-slate-900 mt-1.5">
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700">{{ $vendor->code }}</span>
            </div>
            <div class="text-xs text-slate-500 mt-1">Kategori: {{ $vendor->category ?? '-' }}</div>
        </div>
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Kontak Person (PIC)</span>
            <div class="text-sm font-bold text-slate-900 mt-1.5">{{ $vendor->contact_person ?? '-' }}</div>
            <div class="text-xs text-slate-500 mt-1 font-mono">Telp: {{ $vendor->phone ?? '-' }}</div>
        </div>
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Email Perusahaan</span>
            <div class="text-sm font-mono text-slate-800 mt-1.5">{{ $vendor->email ?? '-' }}</div>
            <div class="text-xs text-slate-400 mt-1">Terdaftar sejak {{ $vendor->created_at?->format('d M Y') ?? '-' }}</div>
        </div>
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Alamat Kantor / Gudang</span>
            <div class="text-xs text-slate-700 mt-1.5 leading-relaxed">
                {{ $vendor->address ?? 'Alamat belum diinput.' }}
            </div>
        </div>
    </div>

    <!-- Riwayat Purchase Orders Terkait -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat Purchase Order Rekanan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kontrak pesanan pembelian yang pernah diterbitkan kepada {{ $vendor->name }}.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                {{ $vendor->procurementOrders->count() }} Kontrak
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nomor PO</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Nilai Kontrak</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Target Tiba</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status Pengiriman</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vendor->procurementOrders as $po)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('web.procures.show', $po->id) }}" class="hover:text-emerald-600 hover:underline">
                                    {{ $po->po_number }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 tabular-nums">
                                Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$po->status" />
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('web.procures.show', $po->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-emerald-700 inline-block shadow-2xs transition">
                                    Rincian &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-1">
                                    <p class="font-semibold text-slate-700 text-sm">Belum Ada PO</p>
                                    <p class="text-slate-400 text-xs">Belum ada kontrak Purchase Order yang diterbitkan untuk mitra rekanan ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
