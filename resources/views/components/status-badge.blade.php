@props(['status'])

@php
    $statusUpper = strtoupper($status ?? 'DRAFT');
    
    $config = match($statusUpper) {
        'DRAFT' => [
            'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
            'dot' => 'bg-slate-400',
            'label' => 'Draft'
        ],
        'SUBMITTED' => [
            'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
            'dot' => 'bg-amber-500',
            'label' => 'Menunggu Approval'
        ],
        'APPROVED' => [
            'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
            'dot' => 'bg-blue-500',
            'label' => 'Disetujui'
        ],
        'REJECTED' => [
            'bg' => 'bg-rose-50 text-rose-800 border-rose-200',
            'dot' => 'bg-rose-500',
            'label' => 'Ditolak'
        ],
        'IN_PROCUREMENT' => [
            'bg' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            'dot' => 'bg-indigo-500',
            'label' => 'Dalam Pengadaan (PO)'
        ],
        'COMPLETED' => [
            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'dot' => 'bg-emerald-500',
            'label' => 'Selesai'
        ],
        'DELIVERED' => [
            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'dot' => 'bg-emerald-500',
            'label' => 'Telah Diterima'
        ],
        default => [
            'bg' => 'bg-slate-100 text-slate-700 border-slate-200',
            'dot' => 'bg-slate-400',
            'label' => $status
        ]
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-2xs font-semibold border {{ $config['bg'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
