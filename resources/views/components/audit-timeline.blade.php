@props(['histories' => []])

<div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
    @forelse($histories as $history)
        <div class="relative">
            <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-emerald-600 bg-white ring-4 ring-emerald-50"></div>
            
            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/70 text-xs space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                        <span class="text-slate-500 font-mono px-1.5 py-0.5 rounded bg-slate-200/60 text-2xs">{{ $history->from_status ?? 'AWAL' }}</span>
                        <span class="text-emerald-600 font-bold">&rarr;</span>
                        <span class="text-emerald-800 font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-2xs">{{ $history->to_status }}</span>
                    </div>
                    <time class="text-2xs text-slate-400 font-mono">
                        {{ $history->created_at?->format('d M Y, H:i:s') ?? '-' }}
                    </time>
                </div>

                <div class="text-slate-700">
                    Eksekutor: <strong class="text-slate-900">{{ $history->actor?->name ?? 'Sistem' }}</strong>
                    @if($history->notes)
                        <div class="mt-2 text-slate-600 italic bg-white p-2.5 rounded-lg border border-slate-200 text-xs shadow-2xs">
                            "{{ $history->notes }}"
                        </div>
                    @endif
                </div>

                <div class="pt-2 border-t border-slate-200/60 text-2xs font-mono text-slate-400 flex flex-wrap gap-x-4 gap-y-1">
                    <span>IP: {{ $history->ip_address ?? '-' }}</span>
                    <span class="truncate max-w-xs" title="{{ $history->user_agent }}">Browser: {{ Str::limit($history->user_agent ?? '-', 45) }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="p-6 text-center text-xs text-slate-400">
            Belum ada jejak riwayat status (audit trail) untuk permohonan ini.
        </div>
    @endforelse
</div>
