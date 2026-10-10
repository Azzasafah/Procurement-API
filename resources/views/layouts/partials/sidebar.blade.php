@php
    $role = auth()->user()?->role ?? 'employee';
    $isManagerOrAdmin = in_array($role, ['manager', 'admin']);
    $isPurchasingOrAdmin = in_array($role, ['purchasing', 'admin']);
    $isAdmin = $role === 'admin';
@endphp

<aside id="sidebar-menu" class="hidden lg:flex w-64 bg-white border-r border-slate-200 flex-col shrink-0 overflow-y-auto select-none">
    <div class="p-4 space-y-6 flex-1">
        
        <!-- Group 1: Alur Utama -->
        <div>
            <div class="px-3 pb-2 text-2xs font-bold text-slate-400 uppercase tracking-wider">Alur Utama</div>
            <div class="space-y-1">
                <a href="{{ route('web.dashboard') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.dashboard') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.dashboard') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <span>Dashboard & Pipeline</span>
                </a>

                <a href="{{ route('web.requests.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.requests.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.requests.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span>Pengajuan Barang</span>
                </a>

                @if($isPurchasingOrAdmin)
                    <a href="{{ route('web.procures.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.procures.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.procures.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span>Pesanan Pembelian (PO)</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Group 2: Logistik & Master -->
        <div>
            <div class="px-3 pb-2 text-2xs font-bold text-slate-400 uppercase tracking-wider">Logistik & Master</div>
            <div class="space-y-1">
                <a href="{{ route('web.stocks.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.stocks.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.stocks.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <span>Stok & Gudang</span>
                </a>

                @if($isPurchasingOrAdmin)
                    <a href="{{ route('web.vendors.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.vendors.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.vendors.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span>Rekanan & Vendor</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Group 3: Manajemen & Laporan (Hanya Ditampilkan Jika Pengguna Memiliki Akses) -->
        @if($isManagerOrAdmin)
        <div>
            <div class="px-3 pb-2 text-2xs font-bold text-slate-400 uppercase tracking-wider">Manajemen & Laporan</div>
            <div class="space-y-1">
                <a href="{{ route('web.reports.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.reports.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                    <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.reports.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span>Laporan & SLA Lead Time</span>
                </a>

                @if($isAdmin)
                    <a href="{{ route('web.users.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.users.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.users.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span>Akun Pengguna</span>
                    </a>

                    <a href="{{ route('web.departments.index') }}" class="group w-full px-3 py-2 rounded-lg text-xs flex items-center gap-3 transition {{ request()->routeIs('web.departments.*') ? 'bg-emerald-50 text-emerald-900 font-bold border-r-2 border-emerald-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium' }}">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center transition {{ request()->routeIs('web.departments.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span>Departemen Divisi</span>
                    </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Bottom Status -->
    <div class="p-4 border-t border-slate-200 bg-slate-50/70">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-800">Sistem Operasional Aktif</span>
        </div>
        <p class="text-2xs text-slate-500">
            Segregation of Duties terverifikasi. Transaksi tercatat aman.
        </p>
    </div>
</aside>
