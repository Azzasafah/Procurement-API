@extends('layouts.app')

@section('title', 'Rincian Pengadaan ' . $procurementRequest->request_number . ' | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action Bar Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.requests.index') }}" class="hover:underline">Pengajuan Barang</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold font-mono">{{ $procurementRequest->request_number }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold font-mono tracking-tight text-slate-900">{{ $procurementRequest->request_number }}</h1>
                <x-status-badge :status="$procurementRequest->status" />
            </div>
        </div>

        <!-- FSM Action Buttons Based on Role and State -->
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('web.requests.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                &larr; Kembali
            </a>

            <!-- Action: Submit Draft -->
            @if($procurementRequest->status === 'DRAFT')
                <form id="form-submit-req" action="{{ route('web.requests.submit', $procurementRequest->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm transition">
                        Kirim ke Manajer (Submit)
                    </button>
                </form>
            @endif

            <!-- Action: Approve / Reject (Manager & Admin) -->
            @if($procurementRequest->status === 'SUBMITTED' && (auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin')))
                <button type="button" onclick="openRejectModal()" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition">
                    Tolak Pengadaan
                </button>
                <form id="form-approve-req" action="{{ route('web.requests.approve', $procurementRequest->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="notes" value="Disetujui oleh manajer divisi.">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                        Setujui Permohonan (Approve)
                    </button>
                </form>
            @endif

            <!-- Action: Issue PO (Purchasing & Admin) -->
            @if($procurementRequest->status === 'APPROVED' && (auth()->user()->hasRole('purchasing') || auth()->user()->hasRole('admin')))
                <button type="button" onclick="openProcureModal()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                    Terbitkan Purchase Order (PO)
                </button>
                <form id="form-complete-direct" action="{{ route('web.requests.complete', $procurementRequest->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="px-4 py-2 rounded-xl border border-emerald-600 text-emerald-700 hover:bg-emerald-50 text-xs font-semibold transition">
                        Selesaikan Langsung
                    </button>
                </form>
            @endif

            <!-- Action: Complete Direct if IN_PROCUREMENT -->
            @if($procurementRequest->status === 'IN_PROCUREMENT' && (auth()->user()->hasRole('purchasing') || auth()->user()->hasRole('admin')))
                <form id="form-complete-po" action="{{ route('web.requests.complete', $procurementRequest->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                        Selesaikan Pengadaan (Complete)
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Staf Pemohon</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $procurementRequest->requester?->name ?? '-' }}</div>
            <div class="text-xs text-slate-500 font-mono">{{ $procurementRequest->requester?->email ?? '-' }}</div>
        </div>
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Departemen / Divisi</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $procurementRequest->department?->name ?? '-' }}</div>
            <div class="text-xs text-slate-500">Kode: {{ $procurementRequest->department?->code ?? '-' }}</div>
        </div>
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Diajukan</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $procurementRequest->created_at?->format('d M Y, H:i') ?? '-' }}</div>
            <div class="text-xs text-slate-500">Submit: {{ $procurementRequest->submitted_at ? \Carbon\Carbon::parse($procurementRequest->submitted_at)->format('d M Y') : 'Belum Submit' }}</div>
        </div>
        <div>
            <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Catatan Kebutuhan</span>
            <div class="text-xs text-slate-800 mt-1 italic leading-relaxed">
                "{{ $procurementRequest->notes ?: 'Tidak ada catatan tambahan.' }}"
            </div>
        </div>
    </div>

    <!-- Rincian Barang Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Rincian Barang yang Diminta</h2>
                <p class="text-xs text-slate-500">Daftar item material, kuantitas, dan estimasi biaya per unit.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 px-2.5 py-1 rounded-lg bg-slate-100">{{ $procurementRequest->items->count() }} Item Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-3 font-semibold uppercase tracking-wider text-2xs">Nama Barang</th>
                        <th class="py-3 px-3 font-semibold uppercase tracking-wider text-2xs">Kategori</th>
                        <th class="py-3 px-3 font-semibold uppercase tracking-wider text-2xs text-center">Jumlah</th>
                        <th class="py-3 px-3 font-semibold uppercase tracking-wider text-2xs">Satuan</th>
                        <th class="py-3 px-3 font-semibold uppercase tracking-wider text-2xs text-right">Estimasi Harga Unit</th>
                        <th class="py-3 px-3 font-semibold uppercase tracking-wider text-2xs text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $grandTotal = 0; @endphp
                    @forelse($procurementRequest->items as $item)
                        @php
                            $subtotal = ($item->quantity ?? 0) * ($item->estimated_price ?? 0);
                            $grandTotal += $subtotal;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-bold text-slate-900">{{ $item->item_name }}</td>
                            <td class="py-3 px-3 text-slate-600 font-medium">{{ $item->category ?? '-' }}</td>
                            <td class="py-3 px-3 text-center font-bold font-mono text-slate-800 tabular-nums">{{ $item->quantity }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $item->unit }}</td>
                            <td class="py-3 px-3 text-right font-mono text-slate-700 tabular-nums">Rp {{ number_format($item->estimated_price ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900 tabular-nums">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada item barang dalam permohonan ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-200 bg-slate-50/50">
                        <td colspan="5" class="py-3 px-3 text-right font-bold text-slate-700">Total Akumulasi Anggaran Estimasi:</td>
                        <td class="py-3 px-3 text-right font-mono font-extrabold text-sm text-emerald-700 tabular-nums">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Informasi Purchase Order Jika Tersedia -->
    @if($procurementRequest->procurementOrders->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Purchase Order Terkait Dokumen Ini</h2>
                    <p class="text-xs text-slate-500">Kontrak pengadaan yang diterbitkan ke mitra rekanan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($procurementRequest->procurementOrders as $po)
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-sm text-slate-900">{{ $po->po_number }}</span>
                            <x-status-badge :status="$po->status" />
                        </div>
                        <div class="text-xs text-slate-600">Vendor: <strong>{{ $po->vendor?->name ?? '-' }}</strong></div>
                        <div class="text-xs text-slate-600">Nilai: <strong class="font-mono text-emerald-700">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</strong></div>
                        <div class="pt-2">
                            <a href="{{ route('web.procures.show', $po->id) }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                                Rincian PO &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Jejak Audit FSM (Audit Trail Timeline) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">Jejak Audit & Riwayat Perubahan Status (FSM Trail)</h2>
            <p class="text-xs text-slate-500">Catatan riwayat status tidak dapat diubah (immutable log) untuk kepatuhan tata kelola.</p>
        </div>

        <x-audit-timeline :histories="$procurementRequest->statusHistories" />
    </div>

</div>

<!-- Modal Reject Partial -->
@include('requests.partials.modal-reject')

<!-- Modal Procure (PO) Partial -->
@include('requests.partials.modal-procure', ['totalEstimatedPrice' => $grandTotal ?? 0])

@endsection
