@extends('layouts.app')

@section('title', 'Dashboard & Pipeline Pengadaan | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Welcome Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2.5 py-0.5 rounded-full text-2xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                    Sistem Operasional FSM
                </span>
                <span class="text-xs font-semibold text-slate-300">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">{{ auth()->user()->department?->name ?? 'Semua Divisi' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Anda masuk dengan peran <strong class="text-slate-800 font-bold uppercase">{{ auth()->user()->role }}</strong>. Kelola alur pengadaan dari inisiasi permohonan, persetujuan manajerial, hingga penerimaan fisik barang di gudang.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="window.openRoleSwitcher()" class="px-3.5 py-2 rounded-xl text-xs font-semibold border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 transition flex items-center gap-2 shadow-2xs">
                <span>⇄</span>
                <span>Ganti Peran</span>
            </button>
            @if(auth()->user()->hasRole('employee') || auth()->user()->hasRole('admin'))
                <a href="{{ route('web.requests.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Buat Pengajuan Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 4 High-End KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Permohonan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Total Permohonan</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">∑</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold font-mono text-slate-900 tabular-nums">
                    {{ $stats['total_requests'] }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Akumulasi pengadaan unit</div>
            </div>
        </div>

        <!-- Metric 2: Dalam Proses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-amber-600 uppercase tracking-wider">Dalam Proses</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">⏱</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold font-mono text-amber-600 tabular-nums">
                    {{ $counts['in_procurement'] ?? $stats['in_procurement'] ?? 0 }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Review atau penerbitan PO</div>
            </div>
        </div>

        <!-- Metric 3: Pengadaan Selesai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-emerald-600 uppercase tracking-wider">Pengadaan Selesai</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">✓</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold font-mono text-emerald-700 tabular-nums">
                    {{ $stats['completed'] ?? 0 }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Barang tiba & terverifikasi</div>
            </div>
        </div>

        <!-- Metric 4: Stok Kritis Gudang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-2xs font-bold text-rose-600 uppercase tracking-wider">Stok Kritis Gudang</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold">!</span>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold font-mono text-rose-600 tabular-nums">
                    {{ $counts['critical_stocks'] ?? $stats['low_stocks'] ?? 0 }}
                </div>
                <div class="text-xs text-slate-500 mt-1">Perlu restock segera</div>
            </div>
        </div>
    </div>

    <!-- Alur Siklus Hidup Pengadaan (Procurement Lifecycle) Interactive Cards -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">Alur Siklus Hidup Pengadaan (Procurement Lifecycle)</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-2xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">4 Tahapan FSM</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Akses langsung modul kerja sesuai peran otorisasi. Klik ikon panduan pada tiap tahap jika ingin melihat detail aturan SoD & SOP.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
            <!-- Step 1 -->
            <a href="{{ route('web.requests.create') }}" class="text-left p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-emerald-50/40 hover:border-emerald-300 transition-all group flex flex-col justify-between min-h-[170px] shadow-2xs">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xs font-bold text-slate-400 uppercase tracking-wider">Langkah 1</span>
                        <span class="px-2 py-0.5 text-2xs font-bold rounded bg-slate-200/80 text-slate-700">Draft</span>
                    </div>
                    <div class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition">1. Permohonan Unit</div>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        Staf menginput daftar kebutuhan dan estimasi biaya ke manajer.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-2xs font-medium text-slate-500">
                    <span class="text-slate-400 text-2xs font-medium">Aktor: Employee</span>
                    <span class="text-emerald-600 font-bold group-hover:translate-x-0.5 transition flex items-center gap-1">
                        <span>Buka Form</span> &rarr;
                    </span>
                </div>
            </a>

            <!-- Step 2 -->
            <a href="{{ route('web.requests.index', ['status' => 'SUBMITTED']) }}" class="text-left p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-amber-50/50 hover:border-amber-300 transition-all group flex flex-col justify-between min-h-[170px] shadow-2xs">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xs font-bold text-amber-700 uppercase tracking-wider">Langkah 2</span>
                        <span class="px-2 py-0.5 text-2xs font-bold rounded bg-amber-100 text-amber-800">Review</span>
                    </div>
                    <div class="text-sm font-bold text-slate-900 group-hover:text-amber-700 transition">2. Review Manajer</div>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        Kepala divisi memverifikasi urgensi kebutuhan dan plafon anggaran.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-2xs font-medium text-slate-500">
                    <span class="text-slate-400 text-2xs font-medium">Aktor: Manager</span>
                    <span class="text-amber-600 font-bold group-hover:translate-x-0.5 transition flex items-center gap-1">
                        <span>Review</span> &rarr;
                    </span>
                </div>
            </a>

            <!-- Step 3 -->
            <a href="{{ route('web.procures.index') }}" class="text-left p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-blue-50/50 hover:border-blue-300 transition-all group flex flex-col justify-between min-h-[170px] shadow-2xs">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xs font-bold text-blue-700 uppercase tracking-wider">Langkah 3</span>
                        <span class="px-2 py-0.5 text-2xs font-bold rounded bg-blue-100 text-blue-800">PO Terbit</span>
                    </div>
                    <div class="text-sm font-bold text-slate-900 group-hover:text-blue-700 transition">3. Penerbitan PO</div>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        Purchasing memilih vendor resmi, negosiasi, dan terbitkan kontrak PO.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-2xs font-medium text-slate-500">
                    <span class="text-slate-400 text-2xs font-medium">Aktor: Purchasing</span>
                    <span class="text-blue-600 font-bold group-hover:translate-x-0.5 transition flex items-center gap-1">
                        <span>Kelola PO</span> &rarr;
                    </span>
                </div>
            </a>

            <!-- Step 4 -->
            <a href="{{ route('web.stocks.index') }}" class="text-left p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-emerald-50/50 hover:border-emerald-300 transition-all group flex flex-col justify-between min-h-[170px] shadow-2xs">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xs font-bold text-emerald-700 uppercase tracking-wider">Langkah 4</span>
                        <span class="px-2 py-0.5 text-2xs font-bold rounded bg-emerald-100 text-emerald-800">Selesai</span>
                    </div>
                    <div class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition">4. Penerimaan & Stok</div>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                        Fisik tiba di logistik, stok terverifikasi otomatis, siklus tuntas.
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-2xs font-medium text-slate-500">
                    <span class="text-slate-400 text-2xs font-medium">Aktor: Warehouse</span>
                    <span class="text-emerald-600 font-bold group-hover:translate-x-0.5 transition flex items-center gap-1">
                        <span>Cek Stok</span> &rarr;
                    </span>
                </div>
            </a>
        </div>
    </div>

    <!-- Data Table: Pengajuan Pengadaan Terkini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pengajuan Pengadaan Terkini</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi pengadaan barang terbaru yang tercatat di sistem.</p>
            </div>
            <a href="{{ route('web.requests.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 hover:underline flex items-center gap-1">
                <span>Lihat Seluruh Daftar</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nomor Request</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Divisi</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Pemohon</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Total Item</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status FSM</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Tanggal Pengajuan</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentRequests as $req)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('web.requests.show', $req->id) }}" class="hover:text-emerald-600 hover:underline inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $req->request_number }}</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">{{ $req->department?->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-700">{{ $req->requester?->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-700 tabular-nums">{{ $req->items->count() }} item</td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$req->status" />
                            </td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $req->created_at?->format('d M Y') ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('web.requests.show', $req->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-emerald-700 inline-block shadow-2xs transition">
                                    Rincian &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500">
                                Belum ada permohonan pengadaan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
