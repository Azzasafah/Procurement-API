@extends('layouts.app', ['title' => 'Laporan Eksekutif & BI'])

@section('content')
<div class="space-y-6">

    <!-- Page Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Business Intelligence & SLA</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Laporan Eksekutif & Business Intelligence</h1>
            <p class="text-xs text-slate-500 mt-1">Metrik kepatuhan SLA, durasi lead time pengadaan, dan tren belanja antar divisi organisasi.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Dokumen Laporan</span>
        </button>
    </div>

    <!-- Lead Time SLA Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Rata-rata Lead Time -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Rata-rata Lead Time</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-mono text-slate-900 tabular-nums">
                    {{ $leadTime->rata_rata_hari ?? 0 }} <span class="text-xs font-sans font-medium text-slate-500">Hari</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Dari permohonan hingga serah terima</div>
            </div>
        </div>

        <!-- Tercepat -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-blue-600 uppercase tracking-wider">Lead Time Tercepat</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-mono text-blue-600 tabular-nums">
                    {{ $leadTime->tercepat_hari ?? 0 }} <span class="text-xs font-sans font-medium text-slate-500">Hari</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Waktu respon & delivery kilat</div>
            </div>
        </div>

        <!-- Terlama -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-amber-600 uppercase tracking-wider">Lead Time Terlama</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-mono text-amber-600 tabular-nums">
                    {{ $leadTime->terlama_hari ?? 0 }} <span class="text-xs font-sans font-medium text-slate-500">Hari</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Evaluasi khusus kendala rekanan</div>
            </div>
        </div>

        <!-- Total Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-emerald-600 uppercase tracking-wider">Total Selesai E2E</span>
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-bold font-mono text-emerald-700 tabular-nums">
                    {{ $leadTime->total_selesai ?? 0 }} <span class="text-xs font-sans font-medium text-slate-500">Order</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Siklus pengadaan tuntas 100%</div>
            </div>
        </div>
    </div>

    <!-- Analytics Mid Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top 5 Departemen -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Top 5 Departemen Paling Aktif</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Frekuensi pengajuan kebutuhan material 3 bulan terakhir.</p>
                </div>
                <span class="text-2xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600">Aktivitas Divisi</span>
            </div>

            @php
                $maxReq = 1;
                foreach($topDepartments as $d) {
                    if ($d->total_request > $maxReq) $maxReq = $d->total_request;
                }
            @endphp

            <div class="space-y-4 pt-1">
                @forelse($topDepartments as $dept)
                    <div class="space-y-1.5">
                        <div class="flex justify-between text-xs">
                            <span class="font-bold text-slate-800">{{ $dept->nama_department }} <span class="text-slate-400 font-mono text-2xs">({{ $dept->kode_department }})</span></span>
                            <span class="font-bold font-mono text-emerald-700 tabular-nums">{{ $dept->total_request }} Pengajuan</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ ($dept->total_request / $maxReq) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada aktivitas pengajuan dalam periode ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Sebaran Siklus FSM -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Distribusi Status Siklus Pengadaan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Sebaran berkas permohonan dalam alur kerja FSM.</p>
                </div>
                <span class="text-2xs font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600">Pipeline FSM</span>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="text-2xs font-semibold text-slate-500 uppercase">Total Seluruh Permohonan</div>
                    <div class="text-2xl font-bold font-mono text-slate-900 mt-1 tabular-nums">
                        {{ $summary['total_request'] ?? 0 }}
                    </div>
                    <div class="text-2xs text-slate-400 mt-0.5">Semua entri tercatat</div>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200">
                    <div class="text-2xs font-semibold text-amber-800 uppercase">Menunggu Persetujuan</div>
                    <div class="text-2xl font-bold font-mono text-amber-700 mt-1 tabular-nums">
                        {{ $summary['menunggu_approval'] ?? 0 }}
                    </div>
                    <div class="text-2xs text-amber-600 mt-0.5">Butuh aksi manajer</div>
                </div>

                <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200">
                    <div class="text-2xs font-semibold text-emerald-800 uppercase">Mitra Rekanan Aktif</div>
                    <div class="text-2xl font-bold font-mono text-emerald-700 mt-1 tabular-nums">
                        {{ $summary['vendor_aktif'] ?? 0 }}
                    </div>
                    <div class="text-2xs text-emerald-600 mt-0.5">Siap menerima PO</div>
                </div>

                <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200">
                    <div class="text-2xs font-semibold text-blue-800 uppercase">Dalam Proses PO</div>
                    <div class="text-2xl font-bold font-mono text-blue-700 mt-1 tabular-nums">
                        @php
                            $inProc = 0;
                            if(isset($summary['per_status'])) {
                                foreach($summary['per_status'] as $st) {
                                    if($st->status === 'IN_PROCUREMENT') $inProc = $st->jumlah;
                                }
                            }
                        @endphp
                        {{ $inProc }}
                    </div>
                    <div class="text-2xs text-blue-600 mt-0.5">PO telah diterbitkan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Belanja per Bulan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tren Permintaan Barang per Kategori per Bulan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Volume material dan pergerakan persediaan per komoditas.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Bulan Periode</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Kategori Barang</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Total Item Terdaftar</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Akumulasi Kuantitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categoryPerMonth as $row)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">{{ $row->bulan }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-2xs border border-slate-200">
                                    {{ $row->kategori }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 tabular-nums">{{ number_format($row->total_item) }} jenis</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-700 tabular-nums">
                                {{ number_format($row->total_quantity) }} unit
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                Belum ada data akumulasi barang per kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
