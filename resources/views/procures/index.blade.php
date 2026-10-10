@extends('layouts.app')

@section('title', 'Purchase Orders (PO) | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Logistik & Kontrak Vendor</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pesanan Pembelian & Logistik (Purchase Orders)</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar kontrak pengadaan resmi yang telah diterbitkan ke mitra rekanan.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200">
                Total {{ $orders->total() ?? $orders->count() }} Purchase Order
            </span>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nomor PO</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Mitra Rekanan / Vendor</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nomor Permohonan</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Nilai Kontrak</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Estimasi Tiba</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status Logistik</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $po)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('web.procures.show', $po->id) }}" class="hover:text-emerald-600 hover:underline inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $po->po_number }}</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $po->vendor?->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">
                                @if($po->request)
                                    <a href="{{ route('web.requests.show', $po->request->id) }}" class="hover:underline text-emerald-600 font-semibold">
                                        {{ $po->request->request_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
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
                                    Rincian PO &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-1">
                                    <p class="font-semibold text-slate-700 text-sm">Belum Ada Purchase Order</p>
                                    <p class="text-slate-400 text-xs">Purchase order diterbitkan oleh purchasing saat permohonan disetujui.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
