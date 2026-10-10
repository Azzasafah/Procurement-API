<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pengadaan Barang & Logistik Internal')</title>
    <meta name="description" content="Sistem ERP Pengadaan Barang, Logistik, dan Manajemen Inventaris Perusahaan.">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="/css/erp.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 bg-slate-50 selection:bg-emerald-600 selection:text-white">

    <!-- Top Navigation Header -->
    @include('layouts.partials.navbar')

    <!-- Main Workspace Container -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Sidebar Navigation -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-50/70 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Flash Notifications -->
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-900 p-1" aria-label="Tutup notifikasi">
                            ✕
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                            <span class="font-medium">{{ session('error') }}</span>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-900 p-1" aria-label="Tutup notifikasi">
                            ✕
                        </button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs shadow-xs">
                        <div class="font-bold mb-1 flex items-center gap-2">
                            <span>Periksa kembali data formulir:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Yield Page Content -->
                @yield('content')

            </div>
        </main>
    </div>

    <!-- ============================================================ -->
    <!-- GLOBAL TAILWIND MODALS POWERED BY ALPINE.JS                 -->
    <!-- ============================================================ -->

    <!-- 1. Global Interactive Confirmation Modal -->
    <div x-data="{
        open: false,
        title: 'Konfirmasi Tindakan',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText: 'Ya, Lanjutkan',
        variant: 'primary',
        callback: null,
        show(opts) {
            this.title = opts.title || 'Konfirmasi Tindakan';
            this.message = opts.message || 'Apakah Anda yakin ingin melanjutkan?';
            this.confirmText = opts.confirmText || 'Ya, Lanjutkan';
            this.variant = opts.variant || 'primary';
            this.callback = opts.onConfirm || null;
            this.open = true;
        },
        confirm() {
            this.open = false;
            if (typeof this.callback === 'function') {
                this.callback();
            }
        }
    }"
    x-init="window.openConfirmModal = (opts) => show(opts)"
    x-show="open"
    @keydown.escape.window="open = false"
    x-cloak
    class="fixed inset-0 z-[100]">
        <!-- Backdrop Covering 100% of Screen (Navbar, Sidebar & Content) -->
        <div x-show="open" 
             @click="open = false"
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Dialog Position -->
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 flex min-h-full items-center justify-center" @click.self="open = false">
            <div x-show="open" 
                 x-transition:enter="ease-out duration-200" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-150" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 @click.away="open = false" 
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">
                
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                             :class="variant === 'danger' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'">
                            <svg x-show="variant === 'danger'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <svg x-show="variant !== 'danger'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-slate-900" x-text="title"></h3>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed" x-text="message"></p>
                        </div>
                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            ✕
                        </button>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="open = false" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition shadow-xs">
                        Batalkan
                    </button>
                    <button type="button" @click="confirm()" 
                            class="px-5 py-2 text-xs font-semibold rounded-xl text-white transition shadow-sm"
                            :class="variant === 'danger' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700'"
                            x-text="confirmText">
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Non-intrusive Helper Script (Dispatches to Navbar Dropdown without Full-screen Popups) -->
    <script>
        window.openRoleSwitcher = function() {
            window.dispatchEvent(new CustomEvent('open-role-dropdown'));
        };
    </script>

    <!-- Stack for Page-Specific Modals (Rendered at Root of Body Above Navbar) -->
    @stack('modals')

    @stack('scripts')
</body>
</html>
