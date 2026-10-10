@extends('layouts.app', ['title' => 'Profil Saya'])

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                <span class="text-xs font-semibold text-slate-500">Pengaturan Akun</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Profil Akun Pengguna</h1>
            <p class="text-xs text-slate-500 mt-1">Informasi identitas akun dan hak akses operasional (SoD) Anda dalam sistem ERP.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- User Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs text-center space-y-4 md:col-span-1 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl font-bold mx-auto shadow-sm shadow-emerald-600/20">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">{{ auth()->user()->name }}</h2>
                    <div class="text-xs text-slate-500 font-mono mt-0.5">{{ auth()->user()->email }}</div>
                </div>
                
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-2xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Peran: {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

            <div class="text-left border-t border-slate-100 pt-4 space-y-3 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Departemen:</span>
                    <span class="font-semibold text-slate-800">{{ auth()->user()->department->name ?? 'Kantor Pusat' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Status Akun:</span>
                    <span class="font-semibold text-emerald-700 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ auth()->user()->is_active ? 'Aktif Terverifikasi' : 'Nonaktif' }}</span>
                    </span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-500">Nomor Telepon:</span>
                    <span class="font-mono text-slate-800">{{ auth()->user()->phone ?? 'Belum terdata' }}</span>
                </div>
            </div>
        </div>

        <!-- Edit Password Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs md:col-span-2 space-y-5">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Perbarui Kata Sandi Akun</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pastikan kata sandi Anda memiliki minimal 8 karakter dengan kombinasi yang aman.</p>
                </div>
                <span class="text-2xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">Keamanan</span>
            </div>

            <form action="{{ route('web.profile.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Saat Ini: <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="current_password" required placeholder="Masukkan kata sandi lama" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kata Sandi Baru: <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" minlength="8" required placeholder="Minimal 8 karakter" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Konfirmasi Kata Sandi Baru: <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" minlength="8" required placeholder="Ulangi kata sandi baru" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-sm transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Simpan Perubahan Sandi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
