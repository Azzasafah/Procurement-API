@extends('layouts.auth')

@section('title', 'Masuk | Sistem Pengadaan & Logistik')

@section('content')
<div class="w-full max-w-5xl mx-auto" x-data="{ activeTab: 'roles', modalFsm: false, modalSod: false }">
    
    <!-- Flash Alerts -->
    @include('layouts.partials.alerts')

    <!-- Main Card Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-[0_20px_60px_-15px_rgba(15,23,42,0.06)] overflow-hidden grid grid-cols-1 lg:grid-cols-12">
        
        <!-- Left Panel: Brand & Platform Value (Editorial Clean) -->
        <div class="lg:col-span-5 bg-slate-50/70 p-7 sm:p-9 border-b lg:border-b-0 lg:border-r border-slate-200/80 flex flex-col justify-between space-y-8">
            <div class="space-y-6">
                <!-- Brand Header -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white shadow-md shadow-emerald-600/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-900 tracking-tight">ProcureFlow</div>
                        <div class="text-2xs text-slate-500 font-medium">Portal Pengadaan & Logistik</div>
                    </div>
                </div>

                <!-- Headline & Intro -->
                <div class="space-y-2.5">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight leading-snug">
                        Pengadaan barang lebih cepat, rapi, dan terukur.
                    </h1>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Kelola seluruh alur permohonan belanja, persetujuan anggaran manajerial, hingga pencatatan stok gudang dalam satu sistem.
                    </p>
                </div>

                <!-- Feature Highlights -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-2xs">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            ✓
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">Persetujuan Bertingkat</div>
                            <div class="text-2xs text-slate-500 leading-relaxed mt-0.5">Verifikasi berjenjang oleh kepala divisi dan tim purchasing dengan audit trail lengkap.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-2xs">
                        <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            📄
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">Penerbitan Purchase Order</div>
                            <div class="text-2xs text-slate-500 leading-relaxed mt-0.5">Pilih rekanan vendor resmi dan terbitkan PO dengan nomor referensi otomatis.</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200/60 shadow-2xs">
                        <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            📦
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-800">Sinkronisasi Stok Gudang</div>
                            <div class="text-2xs text-slate-500 leading-relaxed mt-0.5">Barang yang diterima otomatis masuk ke inventaris dan memantau stok kritis.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Explanations -->
            <div class="pt-5 border-t border-slate-200/60 space-y-2">
                <div class="text-2xs font-semibold text-slate-400 uppercase tracking-wider">Panduan Sistem:</div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="modalFsm = true" class="text-xs text-slate-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1.5 transition">
                        <span>Alur Status Dokumen</span>
                        <span class="text-slate-400">&rarr;</span>
                    </button>
                    <span class="text-slate-300">&bull;</span>
                    <button type="button" @click="modalSod = true" class="text-xs text-slate-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1.5 transition">
                        <span>Pemisahan Wewenang (SoD)</span>
                        <span class="text-slate-400">&rarr;</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login & Demo Role Picker -->
        <div class="lg:col-span-7 p-7 sm:p-9 flex flex-col justify-between space-y-6">
            <div>
                <!-- Form Title -->
                <div class="mb-5">
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Selamat Datang</h2>
                    <p class="text-xs text-slate-500 mt-1">Pilih profil demo untuk langsung masuk, atau gunakan email terdaftar.</p>
                </div>

                <!-- Tabs -->
                <div class="flex p-1 bg-slate-100 rounded-xl mb-5 text-xs font-medium border border-slate-200/60">
                    <button type="button" @click="activeTab = 'roles'" :class="activeTab === 'roles' ? 'bg-white text-slate-900 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 px-3 rounded-lg transition text-center flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Akun Demo (1-Klik)</span>
                    </button>
                    <button type="button" @click="activeTab = 'manual'" :class="activeTab === 'manual' ? 'bg-white text-slate-900 shadow-2xs font-semibold' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 px-3 rounded-lg transition text-center flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Masuk dengan Email</span>
                    </button>
                </div>

                <!-- Tab 1: Demo Persona List -->
                <div x-show="activeTab === 'roles'" x-cloak class="space-y-2.5">
                    
                    <!-- Role 1: Employee -->
                    <form action="{{ route('web.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="employee@procurement.app">
                        <input type="hidden" name="role" value="employee">
                        <button type="submit" class="w-full text-left p-3 rounded-2xl border border-slate-200/80 hover:border-emerald-300 bg-white hover:bg-emerald-50/20 transition-all flex items-center justify-between group shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 font-semibold flex items-center justify-center text-xs group-hover:bg-emerald-100/60 group-hover:text-emerald-800 transition-colors">
                                    BS
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-900 group-hover:text-emerald-900 transition-colors">Budi Santoso</div>
                                    <div class="text-2xs text-slate-500">Staf Pemohon &bull; Divisi Finance</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-2xs font-medium bg-slate-100 text-slate-600 border border-slate-200/80">Staff</span>
                                <span class="text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all text-xs">&rarr;</span>
                            </div>
                        </button>
                    </form>

                    <!-- Role 2: Manager -->
                    <form action="{{ route('web.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="manager@procurement.app">
                        <input type="hidden" name="role" value="manager">
                        <button type="submit" class="w-full text-left p-3 rounded-2xl border border-slate-200/80 hover:border-amber-300 bg-white hover:bg-amber-50/20 transition-all flex items-center justify-between group shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 font-semibold flex items-center justify-center text-xs group-hover:bg-amber-100 transition-colors">
                                    AW
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-900 group-hover:text-amber-900 transition-colors">Andi Wijaya</div>
                                    <div class="text-2xs text-slate-500">Kepala Divisi &bull; Otorisasi Persetujuan</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-2xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">Manager</span>
                                <span class="text-slate-300 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all text-xs">&rarr;</span>
                            </div>
                        </button>
                    </form>

                    <!-- Role 3: Purchasing -->
                    <form action="{{ route('web.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="purchasing@procurement.app">
                        <input type="hidden" name="role" value="purchasing">
                        <button type="submit" class="w-full text-left p-3 rounded-2xl border border-slate-200/80 hover:border-sky-300 bg-white hover:bg-sky-50/20 transition-all flex items-center justify-between group shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 font-semibold flex items-center justify-center text-xs group-hover:bg-sky-100 transition-colors">
                                    SR
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-900 group-hover:text-sky-900 transition-colors">Siti Rahayu</div>
                                    <div class="text-2xs text-slate-500">Tim Pengadaan &bull; Penetapan Vendor & PO</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-2xs font-medium bg-sky-50 text-sky-700 border border-sky-200/60">Purchasing</span>
                                <span class="text-slate-300 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all text-xs">&rarr;</span>
                            </div>
                        </button>
                    </form>

                    <!-- Role 4: Warehouse -->
                    <form action="{{ route('web.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="warehouse@procurement.app">
                        <input type="hidden" name="role" value="warehouse">
                        <button type="submit" class="w-full text-left p-3 rounded-2xl border border-slate-200/80 hover:border-teal-300 bg-white hover:bg-teal-50/20 transition-all flex items-center justify-between group shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 font-semibold flex items-center justify-center text-xs group-hover:bg-teal-100 transition-colors">
                                    DL
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-900 group-hover:text-teal-900 transition-colors">Dewi Lestari</div>
                                    <div class="text-2xs text-slate-500">Logistik Gudang &bull; Verifikasi Fisik & Stok</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-2xs font-medium bg-teal-50 text-teal-700 border border-teal-200/60">Warehouse</span>
                                <span class="text-slate-300 group-hover:text-teal-600 group-hover:translate-x-0.5 transition-all text-xs">&rarr;</span>
                            </div>
                        </button>
                    </form>

                    <!-- Role 5: Admin -->
                    <form action="{{ route('web.switch-role') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="admin@procurement.app">
                        <input type="hidden" name="role" value="admin">
                        <button type="submit" class="w-full text-left p-3 rounded-2xl border border-slate-200/80 hover:border-emerald-400 bg-white hover:bg-emerald-50/30 transition-all flex items-center justify-between group shadow-2xs">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-semibold flex items-center justify-center text-xs group-hover:bg-emerald-200/80 transition-colors">
                                    AD
                                </div>
                                <div>
                                    <div class="text-xs font-semibold text-slate-900 group-hover:text-emerald-950 transition-colors">Admin System</div>
                                    <div class="text-2xs text-slate-500">Akses Penuh &bull; Master Data & Laporan</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-2xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80">Admin</span>
                                <span class="text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all text-xs">&rarr;</span>
                            </div>
                        </button>
                    </form>
                </div>

                <!-- Tab 2: Manual Email Login Form -->
                <div x-show="activeTab === 'manual'" x-cloak class="space-y-4">
                    <form action="{{ route('web.login.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat Email:</label>
                            <input type="email" id="email" name="email" value="{{ old('email', 'admin@procurement.app') }}" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" placeholder="nama@procurement.app">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi:</label>
                            <input type="password" id="password" name="password" value="password" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" placeholder="••••••••">
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-semibold rounded-xl text-xs transition shadow-sm shadow-emerald-600/20 flex items-center justify-center gap-1.5">
                            <span>Masuk ke Akun</span>
                            <span>&rarr;</span>
                        </button>
                    </form>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-2xs text-slate-500 flex items-center justify-between">
                        <span>Kata sandi default semua akun pengujian:</span>
                        <code class="font-mono font-semibold text-slate-700 bg-white px-2 py-0.5 rounded border border-slate-200">password</code>
                    </div>
                </div>
            </div>

            <!-- Footer Meta -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-2xs text-slate-400">
                <span>Versi 1.0</span>
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Sistem Aktif & Terlindungi</span>
                </span>
            </div>
        </div>

    </div>

    <!-- Modal 1: Alur Status Dokumen (FSM) -->
    <div x-show="modalFsm" x-cloak class="relative z-50">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="modalFsm = false"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 flex min-h-full items-center justify-center">
            <div @click.away="modalFsm = false" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Alur Status Pengadaan Barang</h3>
                        <p class="text-2xs text-slate-500 mt-0.5">Siklus dokumen dari inisiasi hingga barang diterima di gudang</p>
                    </div>
                    <button type="button" @click="modalFsm = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-sm">✕</button>
                </div>
                
                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-2xs shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="font-semibold text-slate-800">Draft &rarr; Diajukan (Submitted)</div>
                            <p class="text-2xs text-slate-500 mt-0.5 leading-relaxed">Staf pemohon mengisi daftar kebutuhan barang dan perkiraan harga, lalu mengirimkan pengajuan ke manajer.</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50/50 border border-amber-200/80 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-amber-200 text-amber-800 font-bold flex items-center justify-center text-2xs shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="font-semibold text-slate-800">Review Manajerial (Approved / Rejected)</div>
                            <p class="text-2xs text-slate-500 mt-0.5 leading-relaxed">Kepala divisi memeriksa urgensi dan alokasi dana. Jika disetujui, diteruskan ke bagian pengadaan; jika ditolak, alasan penolakan dicatat.</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-sky-50/50 border border-sky-200/80 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-sky-200 text-sky-800 font-bold flex items-center justify-center text-2xs shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="font-semibold text-slate-800">Proses Pembelian (In Procurement)</div>
                            <p class="text-2xs text-slate-500 mt-0.5 leading-relaxed">Tim purchasing memilih rekanan vendor resmi, menyepakati harga, dan menerbitkan dokumen Purchase Order (PO).</p>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50/50 border border-emerald-200/80 flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-emerald-200 text-emerald-800 font-bold flex items-center justify-center text-2xs shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="font-semibold text-slate-800">Penerimaan & Selesai (Completed)</div>
                            <p class="text-2xs text-slate-500 mt-0.5 leading-relaxed">Barang fisik tiba di gudang logistik, diinspeksi kualitasnya, dan kuantitas stok sistem bertambah secara otomatis.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" @click="modalFsm = false" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2: Info Pemisahan Wewenang (SoD) -->
    <div x-show="modalSod" x-cloak class="relative z-50">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="modalSod = false"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 flex min-h-full items-center justify-center">
            <div @click.away="modalSod = false" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pemisahan Wewenang (Segregation of Duties)</h3>
                        <p class="text-2xs text-slate-500 mt-0.5">Prinsip tata kelola untuk mencegah penyalahgunaan anggaran</p>
                    </div>
                    <button type="button" @click="modalSod = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-sm">✕</button>
                </div>
                
                <div class="space-y-2.5 text-xs text-slate-600 leading-relaxed">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">&bull;</span>
                            <div><strong class="text-slate-800">Staff (Pemohon):</strong> Hanya berwenang membuat pengajuan kebutuhan divisinya sendiri.</div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">&bull;</span>
                            <div><strong class="text-slate-800">Manager:</strong> Memiliki wewenang menyetujui anggaran, namun tidak menerbitkan PO ke vendor.</div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">&bull;</span>
                            <div><strong class="text-slate-800">Purchasing:</strong> Memilih rekanan dan menerbitkan PO, namun tidak dapat membuat pengajuan barang sendiri.</div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-emerald-600 font-bold">&bull;</span>
                            <div><strong class="text-slate-800">Warehouse:</strong> Mengonfirmasi fisik barang di gudang secara independen.</div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="button" @click="modalSod = false" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-900 text-white hover:bg-slate-800 transition">
                        Mengerti
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
