@extends('layouts.app')

@section('title', 'Buat Pengajuan Pengadaan | Sistem ERP')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb / Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('web.requests.index') }}" class="hover:underline">Pengajuan Barang</a>
                <span>&rarr;</span>
                <span class="text-slate-800 font-semibold">Formulir Baru</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Formulir Pengajuan Pengadaan Barang</h1>
        </div>
        <a href="{{ route('web.requests.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
            <span>&larr;</span>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Main Creation Form with Alpine.js Dynamic Items -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-xs">
        <form id="form-create-request" action="{{ route('web.requests.store') }}" method="POST" x-data="procurementForm()" class="space-y-6">
            @csrf

            <!-- General Info -->
            <div class="space-y-1.5">
                <label for="notes" class="block text-xs font-semibold text-slate-800">
                    Alasan / Catatan Kebutuhan Pengadaan: <span class="text-rose-500">*</span>
                </label>
                <textarea id="notes" name="notes" rows="2" required class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs" placeholder="Jelaskan tujuan dan justifikasi operasional kebutuhan barang ini...">{{ old('notes') }}</textarea>
            </div>

            <!-- Dynamic Items Table -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Rincian Barang yang Diajukan</h2>
                        <p class="text-2xs text-slate-500 mt-0.5">Minimal 1 baris barang wajib dimasukkan ke dalam daftar.</p>
                    </div>
                    <button type="button" @click="addItem()" class="px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold hover:bg-emerald-100 shadow-2xs flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Baris</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/60 grid grid-cols-1 sm:grid-cols-12 gap-3 items-end transition hover:border-slate-300">
                            
                            <!-- Nama Barang -->
                            <div class="sm:col-span-3">
                                <label class="block text-2xs font-semibold text-slate-600 mb-1">Nama Barang:</label>
                                <input type="text" :name="`items[${index}][item_name]`" x-model="item.item_name" required placeholder="Nama item" class="w-full text-xs px-2.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                            </div>

                            <!-- Kategori -->
                            <div class="sm:col-span-2">
                                <label class="block text-2xs font-semibold text-slate-600 mb-1">Kategori:</label>
                                <select :name="`items[${index}][category]`" x-model="item.category" required class="w-full text-xs px-2.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="Electronics">Electronics</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Office Equipment">Office Equipment</option>
                                    <option value="Operational">Operational</option>
                                </select>
                            </div>

                            <!-- Kuantitas -->
                            <div class="sm:col-span-2">
                                <label class="block text-2xs font-semibold text-slate-600 mb-1">Jumlah:</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="w-full text-xs px-2.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                            </div>

                            <!-- Satuan -->
                            <div class="sm:col-span-1">
                                <label class="block text-2xs font-semibold text-slate-600 mb-1">Satuan:</label>
                                <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" required placeholder="pcs" class="w-full text-xs px-2 py-2 rounded-lg border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 text-center">
                            </div>

                            <!-- Estimasi Harga Satuan -->
                            <div class="sm:col-span-3">
                                <label class="block text-2xs font-semibold text-slate-600 mb-1">Estimasi Harga (Rp):</label>
                                <input type="number" :name="`items[${index}][estimated_price]`" x-model.number="item.estimated_price" min="0" step="500" class="w-full text-xs px-2.5 py-2 rounded-lg border border-slate-300 bg-white text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600">
                            </div>

                            <!-- Hapus Baris -->
                            <div class="sm:col-span-1 flex justify-center pb-0.5">
                                <button type="button" @click="removeItem(index)" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus baris">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Total Estimasi Card -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 mt-4">
                    <div>
                        <div class="text-xs font-semibold text-slate-700">Total Estimasi Anggaran Pengadaan:</div>
                        <div class="text-2xs text-slate-500">Dihitung otomatis dari kuantitas dan harga satuan.</div>
                    </div>
                    <div class="text-xl font-bold font-mono text-emerald-700 tabular-nums" x-text="formatRupiah(calculateTotal())">
                        Rp 0
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="text-xs text-slate-500">
                    Pengajuan akan disimpan dengan status awal <strong class="text-amber-600 font-semibold">DRAFT</strong>.
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('web.requests.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-2xs transition">
                        Batal
                    </a>
                    <button type="button" @click="confirmSubmit()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span>Simpan Pengajuan (Draft)</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function procurementForm() {
        return {
            items: [
                { item_name: 'Kertas A4 80gr', category: 'Office Supplies', quantity: 10, unit: 'rim', estimated_price: 55000 },
                { item_name: 'Ballpoint Pilot G2', category: 'Office Supplies', quantity: 20, unit: 'pcs', estimated_price: 18000 }
            ],
            addItem() {
                this.items.push({
                    item_name: '',
                    category: 'Office Supplies',
                    quantity: 1,
                    unit: 'pcs',
                    estimated_price: 0
                });
            },
            removeItem(index) {
                if (this.items.length <= 1) {
                    if (typeof window.openConfirmModal === 'function') {
                        window.openConfirmModal({
                            title: 'Pemberitahuan Baris Minimal',
                            message: 'Pengajuan pengadaan wajib memiliki minimal 1 (satu) item barang.',
                            confirmText: 'Mengerti',
                            variant: 'primary',
                            onConfirm: function() {}
                        });
                    } else {
                        alert('Minimal harus ada 1 item barang dalam pengajuan.');
                    }
                    return;
                }
                this.items.splice(index, 1);
            },
            calculateTotal() {
                return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.estimated_price || 0)), 0);
            },
            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(num);
            },
            confirmSubmit() {
                document.getElementById('form-create-request').submit();
            }
        }
    }
</script>
@endpush
