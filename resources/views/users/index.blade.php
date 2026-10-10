@extends('layouts.app', ['title' => 'Manajemen Pengguna'])

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Tata Kelola Akses</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Manajemen Pengguna & Hak Akses (SoD)</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola akun staf, penempatan divisi, dan hak akses peran operasional (Segregation of Duties).</p>
        </div>

        <a href="{{ route('web.users.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs flex items-center gap-2 self-start md:self-auto shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>+ Daftarkan Karyawan Baru</span>
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Nama & Email Staf</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Departemen / Divisi</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Peran (Role SoD)</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs">Status Akun</th>
                        <th class="py-3 px-4 font-semibold uppercase tracking-wider text-2xs text-right">Perubahan Peran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        @php
                            $roleBadges = [
                                'admin' => ['bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'label' => 'ADMIN UTAMA'],
                                'manager' => ['bg' => 'bg-amber-50 text-amber-800 border-amber-200', 'label' => 'MANAGER APPROVAL'],
                                'purchasing' => ['bg' => 'bg-blue-50 text-blue-800 border-blue-200', 'label' => 'PURCHASING (PO)'],
                                'warehouse' => ['bg' => 'bg-teal-50 text-teal-800 border-teal-200', 'label' => 'LOGISTIK GUDANG'],
                                'employee' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => 'EMPLOYEE PEMOHON'],
                            ];
                            $rb = $roleBadges[$user->role] ?? ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'label' => strtoupper($user->role)];
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold flex items-center justify-center text-2xs uppercase">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-2xs text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                {{ $user->department->name ?? 'Belum Ditentukan' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-2xs font-bold border {{ $rb['bg'] }}">
                                    {{ $rb['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-2xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-2xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('web.users.update-role', $user->id) }}" method="POST" class="inline-flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="text-2xs px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-800 focus:ring-1 focus:ring-emerald-500 font-medium">
                                        <option value="employee" {{ $user->role === 'employee' ? 'selected' : '' }}>Employee</option>
                                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="purchasing" {{ $user->role === 'purchasing' ? 'selected' : '' }}>Purchasing</option>
                                        <option value="warehouse" {{ $user->role === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="px-2.5 py-1.5 text-2xs font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 transition shadow-2xs">
                                        Ubah
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                Belum ada data akun pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
