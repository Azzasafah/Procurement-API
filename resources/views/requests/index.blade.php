@extends('layouts.app')

@section('title', 'Daftar Pengajuan Pengadaan | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Top Card & Actions -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Modul Pengadaan</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Pengajuan Pengadaan Barang</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola dan pantau seluruh permohonan pengadaan barang dari setiap divisi operasional.</p>
        </div>

        @if(auth()->user()->hasRole('employee') || auth()->user()->hasRole('admin'))
            <a href="{{ route('web.requests.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs flex items-center gap-2 self-start md:self-auto shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Pengajuan Baru</span>
            </a>
        @endif
    </div>

    <!-- Status Tabs Navigation -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        @php
            $currentStatus = request('status', '');
            $tabs = [
                '' => 'Semua Pengajuan',
                'DRAFT' => 'Draft',
                'SUBMITTED' => 'Menunggu Approval',
                'APPROVED' => 'Disetujui',
                'IN_PROCUREMENT' => 'Dalam Pengadaan (PO)',
                'COMPLETED' => 'Selesai',
                'REJECTED' => 'Ditolak'
            ];
        @endphp

        @foreach($tabs as $val => $label)
            <a href="{{ route('web.requests.index', array_merge(request()->except('page'), ['status' => $val])) }}" class="px-3.5 py-2 rounded-xl font-semibold whitespace-nowrap transition {{ $currentStatus === $val ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900 shadow-2xs' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nomor REQ</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Departemen</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Pemohon</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Total Item</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status FSM</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Tanggal Diajukan</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70 transition-colors">
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
                                    Lihat Rincian &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-1">
                                    <p class="font-semibold text-slate-700 text-sm">Tidak Ada Data</p>
                                    <p class="text-slate-400 text-xs">Belum ada dokumen permohonan pengadaan untuk filter ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
