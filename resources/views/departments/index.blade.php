@extends('layouts.app', ['title' => 'Master Departemen'])

@section('content')
<div class="space-y-6" x-data="{ showAddDept: false }">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Struktur Organisasi</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Master Departemen & Divisi Organisasi</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola unit kerja, penetapan anggaran, dan hierarki divisi dalam tata kelola pengadaan.</p>
        </div>

        @if(auth()->user()->role === 'admin')
            <button type="button" @click="showAddDept = true" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs flex items-center gap-2 self-start md:self-auto shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Departemen Baru</span>
            </button>
        @endif
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Kode Divisi</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nama Departemen</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Jumlah Personel</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Didaftarkan Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($departments as $dept)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-2xs font-semibold">
                                    {{ $dept->code }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 text-sm">{{ $dept->name }}</td>
                            <td class="py-3.5 px-4 text-slate-700">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-2xs">
                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>{{ $dept->users_count ?? $dept->users()->count() }} Karyawan</span>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right text-slate-500 font-mono text-2xs">
                                {{ $dept->created_at ? $dept->created_at->format('d M Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-1">
                                    <p class="font-semibold text-slate-700 text-sm">Belum Ada Departemen</p>
                                    <p class="text-slate-400 text-xs">Tambahkan departemen baru agar karyawan dapat ditempatkan pada divisi kerjanya.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Modal Tambah Departemen (Pure Tailwind + Alpine.js) -->
        <div x-show="showAddDept" x-cloak class="fixed inset-0 z-[100]">
            <div @click="showAddDept = false" class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 flex min-h-full items-center justify-center" @click.self="showAddDept = false">
                <div @click.away="showAddDept = false" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm shadow-xs">
                                +
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Tambah Departemen Baru</h3>
                                <p class="text-2xs text-slate-500">Daftarkan divisi kerja untuk struktur organisasi.</p>
                            </div>
                        </div>
                        <button type="button" @click="showAddDept = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">✕</button>
                    </div>

                    <form action="{{ route('web.departments.store') }}" method="POST">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Nama Lengkap Departemen: <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="name" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs" placeholder="Contoh: Divisi Logistik & Rantai Pasok" required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Kode Singkatan (Maks 10 Karakter): <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="code" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition uppercase font-mono shadow-2xs" placeholder="Contoh: LOG" maxlength="10" required>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <button type="button" @click="showAddDept = false" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                                Simpan Departemen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
