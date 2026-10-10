@push('modals')
<div id="modal-procure" onclick="if(event.target === this) closeProcureModal()" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <!-- Header -->
        <div class="p-6 border-b border-blue-100 bg-blue-50/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm shadow-xs">
                    PO
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Penerbitan Kontrak Purchase Order (PO)</h3>
                    <p class="text-xs text-blue-700/90 mt-0.5">Penetapan vendor rekanan dan komitmen jadwal pengiriman</p>
                </div>
            </div>
            <button type="button" onclick="closeProcureModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">✕</button>
        </div>

        <form action="{{ route('web.requests.procure', $procurementRequest->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label for="vendor_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Rekanan / Vendor Terverifikasi: <span class="text-rose-500">*</span>
                    </label>
                    <select id="vendor_id" name="vendor_id" required class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                        <option value="">-- Pilih Vendor Rekanan --</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->code }} • {{ $v->category }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="total_amount" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Total Nilai Kontrak PO (Rupiah): <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="total_amount" name="total_amount" min="1000" step="1000" required value="{{ $totalEstimatedPrice > 0 ? $totalEstimatedPrice : '' }}" placeholder="Contoh: 15000000" class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition font-mono shadow-2xs">
                </div>

                <div>
                    <label for="expected_delivery_date" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Target Tanggal Tiba (Delivery Date): <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="expected_delivery_date" name="expected_delivery_date" required min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ now()->addDays(7)->format('Y-m-d') }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>

                <div>
                    <label for="procure_notes" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Catatan Kontrak / Syarat Pengiriman (Opsional):
                    </label>
                    <input type="text" id="procure_notes" name="notes" placeholder="Contoh: Pengiriman via armada vendor ke Gudang Logistik B" class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition shadow-2xs">
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeProcureModal()" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                    Batalkan
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-blue-600 hover:bg-blue-700 text-white transition shadow-sm">
                    Terbitkan Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openProcureModal() {
        const modal = document.getElementById('modal-procure');
        modal.classList.remove('hidden');
    }
    function closeProcureModal() {
        document.getElementById('modal-procure').classList.add('hidden');
    }
</script>
@endpush
