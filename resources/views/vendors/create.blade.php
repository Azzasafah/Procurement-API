@extends('layouts.app')

@section('title', 'Daftarkan Vendor Baru | Sistem ERP')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.vendors.index') }}" class="hover:underline">Rekanan & Vendor</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold">Registrasi Baru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Registrasi Rekanan Vendor Baru</h1>
        </div>
        <a href="{{ route('web.vendors.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
            <span>&larr;</span>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
        <form action="{{ route('web.vendors.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Kode Rekanan: <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code', 'VND-' . rand(100, 999)) }}" required placeholder="VND-005" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs">
                </div>

                <div>
                    <label for="category" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Kategori Bidang Usaha:
                    </label>
                    <input type="text" id="category" name="category" value="{{ old('category', 'Office Supplies') }}" placeholder="Contoh: Electronics / Furniture" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                </div>
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold text-slate-800 mb-1.5">
                    Nama Perusahaan / Rekanan: <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: PT Sumber Rezeki Logistik" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="contact_person" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Kontak Person (PIC):
                    </label>
                    <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="Nama PIC" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Email Perusahaan:
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="vendor@mitra.co.id" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Nomor Telepon:
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="021-xxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition font-mono shadow-2xs">
                </div>
            </div>

            <div>
                <label for="address" class="block text-xs font-semibold text-slate-800 mb-1.5">
                    Alamat Domisili Kantor / Gudang:
                </label>
                <textarea id="address" name="address" rows="2" placeholder="Alamat lengkap rekanan vendor..." class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">{{ old('address') }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-500">Status vendor otomatis terverifikasi aktif.</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('web.vendors.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Rekanan Baru</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
