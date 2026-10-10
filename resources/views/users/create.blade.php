@extends('layouts.app', ['title' => 'Tambah Karyawan Baru'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.users.index') }}" class="hover:underline">Manajemen Pengguna</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold">Registrasi Baru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Akun Karyawan</h1>
        </div>
        <a href="{{ route('web.users.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
            <span>&larr;</span>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
        <form action="{{ route('web.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Nama Lengkap: <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs" placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Alamat Email Perusahaan: <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs" placeholder="nama@perusahaan.co.id" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Kata Sandi Default: <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs" placeholder="Minimal 8 karakter" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Nomor Telepon:
                    </label>
                    <input type="text" name="phone" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs" placeholder="0812xxxxxxxx" value="{{ old('phone') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Departemen / Divisi: <span class="text-rose-500">*</span>
                    </label>
                    <select name="department_id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs" required>
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Peran & Tanggung Jawab (SoD Role): <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs" required>
                        <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee (Pembuat Request)</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager (Pemberi Approval)</option>
                        <option value="purchasing" {{ old('role') == 'purchasing' ? 'selected' : '' }}>Purchasing (Penerbit PO)</option>
                        <option value="warehouse" {{ old('role') == 'warehouse' ? 'selected' : '' }}>Warehouse (Pengelola Gudang)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Administrator Penuh)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-500">Akun baru akan aktif dan siap digunakan login.</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('web.users.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Akun Karyawan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
