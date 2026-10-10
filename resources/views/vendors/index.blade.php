@extends('layouts.app')

@section('title', 'Katalog Vendor Rekanan | Sistem ERP')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Master Data Rekanan</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Mitra Rekanan & Vendor Terverifikasi</h1>
            <p class="text-xs text-slate-500 mt-1">Katalog penyedia barang dan jasa resmi yang memenuhi standar kualifikasi operasional.</p>
        </div>

        @if(auth()->user()->hasRole('purchasing') || auth()->user()->hasRole('admin'))
            <a href="{{ route('web.vendors.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs flex items-center gap-2 self-start md:self-auto shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Daftarkan Vendor Baru</span>
            </a>
        @endif
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Kode Vendor</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nama Perusahaan</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Kategori Bidang</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Kontak Person</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Email</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Telepon</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($vendors as $vnd)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-2xs font-semibold">{{ $vnd->code }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $vnd->name }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $vnd->category ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-700 font-medium">{{ $vnd->contact_person ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-mono text-slate-600">{{ $vnd->email ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono">{{ $vnd->phone ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-semibold {{ $vnd->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $vnd->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    <span>{{ $vnd->is_active ? 'Terverifikasi Aktif' : 'Non-aktif' }}</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('web.vendors.show', $vnd->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-emerald-700 inline-block shadow-2xs transition">
                                    Profil &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-1">
                                    <p class="font-semibold text-slate-700 text-sm">Belum Ada Rekanan</p>
                                    <p class="text-slate-400 text-xs">Daftarkan rekanan baru agar dapat dipilih saat penerbitan Purchase Order.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
