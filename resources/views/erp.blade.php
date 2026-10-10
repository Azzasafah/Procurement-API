<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengadaan Barang & Logistik Internal</title>
    <meta name="description" content="Antarmuka tata kelola siklus pengadaan barang dan logistik perusahaan terintegrasi.">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#0b0f19',
                        },
                        emerald: {
                            700: '#047857',
                            800: '#065f46',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/css/erp.css">
</head>
<body class="h-full flex flex-col bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased selection:bg-emerald-700 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="bg-slate-900 text-white border-b border-slate-800 shrink-0 sticky top-0 z-40">
        <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Brand & Mobile Toggle -->
            <div class="flex items-center gap-3">
                <button id="mobile-menu-toggle" type="button" class="lg:hidden p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Buka navigasi menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2.5 cursor-pointer" onclick="ERP.switchView('pipeline')">
                    <div class="w-8 h-8 rounded bg-emerald-700 flex items-center justify-center font-bold text-white text-sm tracking-wider">
                        EP
                    </div>
                    <div>
                        <div class="font-bold text-sm tracking-tight text-white leading-tight">Sistem Pengadaan Barang</div>
                        <div class="text-2xs text-slate-400">Portal Logistik Internal</div>
                    </div>
                </div>
            </div>

            <!-- Role Switcher & User Profile Bar -->
            <div class="flex items-center gap-3">
                <!-- Role Switcher for Beginners/Testing -->
                <div class="hidden sm:flex items-center gap-2 bg-slate-800/80 px-2.5 py-1 rounded-lg border border-slate-700">
                    <label for="role-quick-switcher" class="text-2xs font-semibold text-slate-300">Pilih Akun:</label>
                    <select id="role-quick-switcher" class="bg-transparent text-xs font-medium text-emerald-400 focus:outline-none cursor-pointer">
                        <option value="employee@procurement.app" class="bg-slate-900 text-white">Budi Santoso (Staff Pemohon)</option>
                        <option value="manager@procurement.app" class="bg-slate-900 text-white">Andi Wijaya (Manajer Approver)</option>
                        <option value="purchasing@procurement.app" class="bg-slate-900 text-white">Siti Rahayu (Tim Pengadaan)</option>
                        <option value="warehouse@procurement.app" class="bg-slate-900 text-white">Dewi Lestari (Petugas Gudang)</option>
                        <option value="admin@procurement.app" class="bg-slate-900 text-white">Admin System (Akses Penuh)</option>
                    </select>
                </div>

                <!-- Active User Summary -->
                <div class="hidden md:flex flex-col text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        <span id="active-user-badge" class="text-xs font-bold text-white">Memuat...</span>
                        <span id="active-user-role" class="px-1.5 py-0.5 text-2xs font-semibold rounded bg-emerald-800 text-emerald-100">ROLE</span>
                    </div>
                    <span id="active-user-dept" class="text-2xs text-slate-400">Departemen</span>
                </div>

                <!-- Theme Toggle Button -->
                <button type="button" onclick="ERP.toggleTheme()" class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 min-h-[44px] min-w-[44px] flex items-center justify-center transition-colors" title="Ganti Tema Terang/Gelap" aria-label="Ganti Tema">
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                <!-- Logout -->
                <button type="button" onclick="ERP.logout()" class="p-2 rounded-lg text-slate-300 hover:text-rose-400 hover:bg-slate-800 min-h-[44px] min-w-[44px] flex items-center justify-center" title="Keluar dari Akun" aria-label="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- App Body: Sidebar + Main Content -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Sidebar Navigation -->
        <aside id="sidebar-menu" class="hidden lg:flex w-64 bg-slate-900 border-r border-slate-800 flex-col shrink-0 overflow-y-auto">
            <div class="p-4 space-y-1">
                <div class="px-3 py-2 text-2xs font-bold text-slate-500 uppercase tracking-wider">Alur Utama</div>
                
                <button type="button" onclick="ERP.switchView('pipeline')" data-view-nav="pipeline" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 bg-slate-800 text-white min-h-[44px]">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Peta Alur Pengadaan</span>
                </button>

                <button type="button" onclick="ERP.switchView('requests')" data-view-nav="requests" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Pengajuan Barang</span>
                </button>

                <button id="nav-item-procures" type="button" onclick="ERP.switchView('procures')" data-view-nav="procures" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Pesanan Pembelian (PO)</span>
                </button>

                <div class="pt-4 px-3 py-2 text-2xs font-bold text-slate-500 uppercase tracking-wider">Logistik & Data Master</div>

                <button type="button" onclick="ERP.switchView('stocks')" data-view-nav="stocks" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Stok & Gudang</span>
                </button>

                <button type="button" onclick="ERP.switchView('vendors')" data-view-nav="vendors" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Rekanan & Vendor</span>
                </button>

                <div class="pt-4 px-3 py-2 text-2xs font-bold text-slate-500 uppercase tracking-wider">Manajemen & Laporan</div>

                <button id="nav-item-reports" type="button" onclick="ERP.switchView('reports')" data-view-nav="reports" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan & SLA Lead Time</span>
                </button>

                <button id="nav-item-users" type="button" onclick="ERP.switchView('users')" data-view-nav="users" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Akun Pengguna</span>
                </button>

                <button id="nav-item-departments" type="button" onclick="ERP.switchView('departments')" data-view-nav="departments" class="w-full text-left px-3 py-2.5 rounded-lg text-xs font-semibold flex items-center gap-3 text-slate-300 hover:bg-slate-800/60 min-h-[44px]">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>Departemen Divisi</span>
                </button>
            </div>

            <div class="mt-auto p-4 border-t border-slate-800 text-2xs text-slate-400 leading-relaxed">
                <div>Versi Sistem: <strong class="text-slate-300">v1.0.0 Enterprise</strong></div>
                <div>Status Koneksi: <span class="text-emerald-400 font-semibold">Tersambung ke API</span></div>
            </div>
        </aside>

        <!-- Main Dynamic Content Workspace -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div id="main-content-area" class="max-w-7xl mx-auto">
                <div class="text-center py-24 text-slate-500 text-sm">
                    Memuat antarmuka sistem pengadaan...
                </div>
            </div>
        </main>
    </div>

    <!-- Universal Modal Window -->
    <div id="erp-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div id="erp-modal-backdrop" class="modal-backdrop fixed inset-0 transition-opacity"></div>
        <div class="min-h-full flex items-center justify-center p-4">
            <div id="erp-modal-content" class="relative bg-white dark:bg-slate-800 rounded-lg max-w-2xl w-full shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Modal content injected via JS -->
            </div>
        </div>
    </div>

    <!-- Toast Notification Message -->
    <div id="erp-toast" class="hidden" role="alert">
        <span id="erp-toast-message">Pesan notifikasi</span>
    </div>

    <!-- ERP Client Script -->
    <script src="/js/erp.js"></script>
</body>
</html>
