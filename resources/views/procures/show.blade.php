@extends('layouts.app')

@section('title', 'Rincian Purchase Order ' . $order->po_number . ' | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.procures.index') }}" class="hover:underline">Pesanan Pembelian (PO)</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold font-mono">{{ $order->po_number }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold font-mono tracking-tight text-slate-900">{{ $order->po_number }}</h1>
                <x-status-badge :status="$order->status" />
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('web.procures.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                <span>&larr;</span>
                <span>Kembali ke Daftar PO</span>
            </a>

            @if($order->status !== 'DELIVERED')
                <form id="form-deliver-order" action="{{ route('web.procures.deliver', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="button" onclick="confirmDelivery()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Konfirmasi Barang Tiba (Deliver)</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- PO Details & Vendor Profile Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Informasi Kontrak PO -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                        PO
                    </div>
                    <h2 class="text-sm font-bold text-slate-900">Informasi Kontrak Pengadaan</h2>
                </div>
                <span class="text-2xs font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">Dokumen Sah</span>
            </div>

            <div class="space-y-3.5 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Nomor PO Resmi:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $order->po_number }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Nomor Request Terkait:</span>
                    <span class="font-mono font-semibold text-emerald-600">
                        @if($order->request)
                            <a href="{{ route('web.requests.show', $order->request->id) }}" class="hover:underline flex items-center gap-1">
                                <span>{{ $order->request->request_number }}</span>
                                <span>&rarr;</span>
                            </a>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Total Nilai Kontrak:</span>
                    <span class="font-mono font-bold text-base text-emerald-700">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Target Tanggal Tiba:</span>
                    <span class="font-semibold text-slate-800">
                        {{ $order->expected_delivery_date ? \Carbon\Carbon::parse($order->expected_delivery_date)->format('d F Y') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Diterbitkan Oleh:</span>
                    <span class="font-semibold text-slate-800">{{ $order->createdBy?->name ?? 'Tim Purchasing' }}</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-500">Tanggal Terbit:</span>
                    <span class="text-slate-600">{{ $order->created_at?->format('d M Y, H:i') ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Profil Vendor Rekanan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs">
                        VN
                    </div>
                    <h2 class="text-sm font-bold text-slate-900">Mitra Rekanan / Vendor</h2>
                </div>
                @if($order->vendor)
                    <a href="{{ route('web.vendors.show', $order->vendor->id) }}" class="text-2xs font-semibold text-blue-600 hover:underline">
                        Lihat Profil &rarr;
                    </a>
                @endif
            </div>

            @if($order->vendor)
                <div class="space-y-3.5 text-xs">
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Nama Perusahaan:</span>
                        <span class="font-bold text-slate-900">{{ $order->vendor->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Kode Rekanan:</span>
                        <span class="font-mono font-semibold text-slate-700 px-2 py-0.5 rounded bg-slate-100">{{ $order->vendor->code }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Kategori Bidang:</span>
                        <span class="text-slate-800 font-medium">{{ $order->vendor->category ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Kontak Person:</span>
                        <span class="text-slate-800 font-medium">{{ $order->vendor->contact_person ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-slate-50">
                        <span class="text-slate-500">Email Korespondensi:</span>
                        <span class="font-mono text-slate-600">{{ $order->vendor->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-500">Nomor Telepon:</span>
                        <span class="text-slate-600 font-mono">{{ $order->vendor->phone ?? '-' }}</span>
                    </div>
                </div>
            @else
                <div class="text-xs text-slate-500 py-6 text-center">Data mitra rekanan tidak ditemukan atau telah dihapus.</div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
    function confirmDelivery() {
        document.getElementById('form-deliver-order').submit();
    }
</script>
@endpush
@endsection
