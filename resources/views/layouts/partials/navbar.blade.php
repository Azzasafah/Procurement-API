<header class="bg-white border-b border-slate-200 shrink-0 sticky top-0 z-30 shadow-2xs">
    <div class="w-full px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <!-- Brand & Mobile Toggle -->
        <div class="flex items-center gap-3">
            <button id="mobile-sidebar-toggle" type="button" onclick="const sb = document.getElementById('sidebar-menu'); if (sb) { sb.classList.toggle('hidden'); sb.classList.toggle('flex'); }" class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition" aria-label="Buka navigasi menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <a href="{{ route('web.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center font-bold text-white text-xs shadow-xs group-hover:bg-emerald-700 transition">
                    EP
                </div>
                <div>
                    <div class="font-bold text-sm text-slate-900 flex items-center gap-1.5 leading-tight">
                        <span>Sistem Pengadaan Barang</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 uppercase tracking-wider">ERP</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-normal leading-tight">Portal Logistik & Tata Kelola Internal</div>
                </div>
            </a>
        </div>

        <!-- Role Switcher & User Profile Bar -->
        <div class="flex items-center gap-2.5 sm:gap-3">
            @auth
                <!-- Dropdown Role Switcher & User Profile (Inline Popover, No Blocking Modal) -->
                <div x-data="{ openMenu: false }" 
                     @open-role-dropdown.window="openMenu = true" 
                     class="relative">
                    <button type="button" 
                            @click="openMenu = !openMenu" 
                            class="flex items-center gap-2.5 pl-2.5 py-1 pr-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shadow-2xs group" 
                            title="Klik untuk ganti peran atau lihat pengaturan akun"
                            aria-expanded="false">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 group-hover:bg-emerald-200 font-bold text-xs flex items-center justify-center shrink-0 transition">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <div class="text-xs font-bold text-slate-900 leading-tight group-hover:text-emerald-700 transition flex items-center gap-1.5">
                                <span>{{ auth()->user()->name }}</span>
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200/80">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>
                            <div class="text-2xs text-slate-500 font-medium leading-tight">
                                {{ auth()->user()->department?->name ?? 'Semua Divisi' }}
                            </div>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 transition transform duration-150" :class="{ 'rotate-180': openMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <!-- Dropdown Popover Menu (No full-screen modal! No dark backdrop!) -->
                    <div x-show="openMenu" 
                         @click.outside="openMenu = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         x-cloak
                         class="absolute right-0 mt-2 w-84 sm:w-96 rounded-2xl bg-white shadow-2xl border border-slate-200 z-50 overflow-hidden divide-y divide-slate-100">
                        
                        <!-- Header with Current Identity -->
                        <div class="px-4 py-3 bg-slate-50/90">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-xs font-bold text-slate-900">Simulasi Ganti Peran</span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-2xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                                    <span>Aktif: {{ strtoupper(auth()->user()->role) }}</span>
                                </span>
                            </div>
                            <p class="text-2xs text-slate-500 mt-1 leading-relaxed">
                                Pilih akun demo untuk menguji wewenang Segregation of Duties:
                            </p>
                        </div>

                        <!-- 5 Demo Role Switcher Buttons -->
                        <div class="p-2 space-y-1 max-h-[320px] overflow-y-auto">
                            @php
                                $demoRoles = [
                                    [
                                        'role' => 'employee',
                                        'email' => 'employee@procurement.app',
                                        'name' => 'Budi Santoso',
                                        'desc' => 'Staf Pemohon Pengadaan',
                                        'badge' => 'Staff',
                                        'badgeColor' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'initial' => 'BS',
                                        'avatar' => 'bg-slate-100 text-slate-700'
                                    ],
                                    [
                                        'role' => 'manager',
                                        'email' => 'manager@procurement.app',
                                        'name' => 'Andi Wijaya',
                                        'desc' => 'Kepala Divisi Approver',
                                        'badge' => 'Manager',
                                        'badgeColor' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'initial' => 'AW',
                                        'avatar' => 'bg-amber-50 text-amber-700'
                                    ],
                                    [
                                        'role' => 'purchasing',
                                        'email' => 'purchasing@procurement.app',
                                        'name' => 'Siti Rahayu',
                                        'desc' => 'Tim Pengadaan & Penerbit PO',
                                        'badge' => 'Purchasing',
                                        'badgeColor' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'initial' => 'SR',
                                        'avatar' => 'bg-sky-50 text-sky-700'
                                    ],
                                    [
                                        'role' => 'warehouse',
                                        'email' => 'warehouse@procurement.app',
                                        'name' => 'Dewi Lestari',
                                        'desc' => 'Logistik & Petugas Gudang',
                                        'badge' => 'Warehouse',
                                        'badgeColor' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'initial' => 'DL',
                                        'avatar' => 'bg-teal-50 text-teal-700'
                                    ],
                                    [
                                        'role' => 'admin',
                                        'email' => 'admin@procurement.app',
                                        'name' => 'Admin System',
                                        'desc' => 'Akses Penuh Seluruh Modul',
                                        'badge' => 'Admin',
                                        'badgeColor' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'initial' => 'AD',
                                        'avatar' => 'bg-emerald-100 text-emerald-800'
                                    ]
                                ];
                            @endphp

                            @foreach($demoRoles as $dr)
                                @php $isActive = (auth()->user()?->role === $dr['role']); @endphp
                                <form action="{{ route('web.switch-role') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $dr['email'] }}">
                                    <input type="hidden" name="role" value="{{ $dr['role'] }}">
                                    <button type="submit" 
                                            class="w-full text-left p-2.5 rounded-xl transition flex items-center justify-between group {{ $isActive ? 'bg-emerald-50/80 border border-emerald-300 ring-1 ring-emerald-300' : 'hover:bg-slate-50 border border-transparent' }}">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-xl {{ $dr['avatar'] }} font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ $dr['initial'] }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                                    <span class="truncate">{{ $dr['name'] }}</span>
                                                    @if($isActive)
                                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded-md whitespace-nowrap">
                                                            ✓ Aktif
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-2xs text-slate-400 truncate mt-0.5">{{ $dr['desc'] }}</div>
                                            </div>
                                        </div>
                                        <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-semibold border {{ $dr['badgeColor'] }} shrink-0 whitespace-nowrap">
                                            {{ $dr['badge'] }}
                                        </span>
                                    </button>
                                </form>
                            @endforeach
                        </div>

                        <!-- Footer: Profile Link & Logout (Direct submit without ugly browser alert) -->
                        <div class="p-2.5 bg-slate-50/90 flex items-center justify-between text-xs">
                            <a href="{{ route('web.profile') }}" class="px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:text-emerald-700 hover:bg-slate-200/60 rounded-lg transition flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Lihat Profil</span>
                            </a>
                            <form action="{{ route('web.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-lg transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth

            @guest
                <!-- Guest Actions -->
                <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1.5">
                    <span>Masuk / Login</span>
                    <span>&rarr;</span>
                </a>
            @endguest
        </div>
    </div>
</header>
