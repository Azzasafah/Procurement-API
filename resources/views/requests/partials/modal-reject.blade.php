@push('modals')
<div id="modal-reject" onclick="if(event.target === this) closeRejectModal()" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-sm">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <!-- Header -->
        <div class="p-6 border-b border-rose-100 bg-rose-50/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 font-bold flex items-center justify-center text-sm shadow-xs">
                    ✕
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Konfirmasi Penolakan Pengadaan</h3>
                    <p class="text-xs text-rose-700/90 mt-0.5">Penolakan wajib menyertakan alasan tertulis untuk audit trail</p>
                </div>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">✕</button>
        </div>

        <form action="{{ route('web.requests.reject', $procurementRequest->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Sesuai standar audit dan GCG perusahaan, permohonan yang ditolak akan dicatat permanen dalam riwayat FSM bersama data alasan dan identitas approver.
                </p>

                <div>
                    <label for="reason" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alasan Penolakan: <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="3" required placeholder="Contoh: Anggaran belum dialokasikan untuk kuartal ini atau spesifikasi barang tidak sesuai SOP..." class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition shadow-2xs"></textarea>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                    Batalkan
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white transition shadow-sm">
                    Tolak Permohonan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() {
        const modal = document.getElementById('modal-reject');
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('reason').focus(), 100);
    }
    function closeRejectModal() {
        document.getElementById('modal-reject').classList.add('hidden');
    }
</script>
@endpush
