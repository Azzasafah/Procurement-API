@props([
    'title',
    'value',
    'subtitle' => null,
    'badge' => null,
    'theme' => 'default'
])

@php
    $accentColor = match($theme) {
        'emerald' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
        'blue' => 'text-blue-700 bg-blue-50 border-blue-200',
        'amber' => 'text-amber-700 bg-amber-50 border-amber-200',
        'rose' => 'text-rose-700 bg-rose-50 border-rose-200',
        default => 'text-slate-700 bg-slate-50 border-slate-200'
    };
@endphp

<div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all group">
    <div class="flex items-center justify-between mb-3">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $title }}</span>
        @if($badge)
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $accentColor }} border">
                {{ $badge }}
            </span>
        @endif
    </div>
    <div class="text-3xl font-extrabold font-mono text-slate-900 tracking-tight num-tabular">
        {{ $value }}
    </div>
    @if($subtitle)
        <div class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">
            {{ $subtitle }}
        </div>
    @endif
</div>
