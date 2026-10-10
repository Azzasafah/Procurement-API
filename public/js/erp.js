const ERP = {
  state: {
    token: localStorage.getItem('procurement_token') || '',
    user: JSON.parse(localStorage.getItem('procurement_user') || 'null'),
    activeView: 'pipeline',
    departments: [],
    vendors: [],
    stocks: [],
    requests: [],
    procures: [],
    reportsSummary: null,
    leadTime: null,
    theme: localStorage.getItem('procurement_theme') || 'light'
  },

  demoAccounts: [
    { role: 'employee', label: 'Budi Santoso (Staff Pemohon)', email: 'employee@procurement.app', desc: 'Bisa membuat draft dan mengirim pengajuan barang.' },
    { role: 'manager', label: 'Andi Wijaya (Manajer Approver)', email: 'manager@procurement.app', desc: 'Bisa menyetujui atau menolak permohonan pengadaan.' },
    { role: 'purchasing', label: 'Siti Rahayu (Tim Pengadaan)', email: 'purchasing@procurement.app', desc: 'Bisa menerbitkan PO ke vendor dan konfirmasi penerimaan barang.' },
    { role: 'warehouse', label: 'Dewi Lestari (Petugas Gudang)', email: 'warehouse@procurement.app', desc: 'Bisa kelola master stok dan simulasi pengecekan stok konkurensi.' },
    { role: 'admin', label: 'Admin System (Akses Penuh)', email: 'admin@procurement.app', desc: 'Akses seluruh modul, master data, dan jejak audit.' }
  ],

  async init() {
    this.applyTheme(this.state.theme);
    this.setupEventListeners();

    if (this.state.token && this.state.user) {
      await this.verifySession();
    } else {
      await this.quickLogin('employee@procurement.app', 'password');
    }
  },

  applyTheme(theme) {
    this.state.theme = theme;
    localStorage.setItem('procurement_theme', theme);
    const html = document.documentElement;
    if (theme === 'dark') {
      html.classList.add('dark');
    } else {
      html.classList.remove('dark');
    }
    const themeLabel = document.getElementById('theme-toggle-label');
    if (themeLabel) {
      themeLabel.textContent = theme === 'dark' ? 'Mode Terang' : 'Mode Gelap';
    }
  },

  toggleTheme() {
    const nextTheme = this.state.theme === 'dark' ? 'light' : 'dark';
    this.applyTheme(nextTheme);
  },

  extractList(res) {
    if (Array.isArray(res?.result?.data)) return res.result.data;
    if (Array.isArray(res?.result)) return res.result;
    return [];
  },

  async api(endpoint, options = {}) {
    const url = `/api/v1${endpoint}`;
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(this.state.token ? { 'Authorization': `Bearer ${this.state.token}` } : {})
    };

    try {
      const response = await fetch(url, {
        ...options,
        headers: { ...headers, ...(options.headers || {}) }
      });

      const data = await response.json().catch(() => null);

      if (!response.ok) {
        let errorMsg = data?.meta?.message || data?.message;
        if (data?.errors) {
          const firstErrKey = Object.keys(data.errors)[0];
          if (firstErrKey && data.errors[firstErrKey][0]) {
            errorMsg = data.errors[firstErrKey][0];
          }
        }
        throw new Error(errorMsg || `Terjadi kesalahan (Kode: ${response.status})`);
      }

      return data;
    } catch (error) {
      console.error(`API Error [${endpoint}]:`, error);
      throw error;
    }
  },

  async verifySession() {
    try {
      const res = await this.api('/auth/me');
      if (res?.result) {
        this.state.user = res.result;
        localStorage.setItem('procurement_user', JSON.stringify(res.result));
        this.renderUserInterface();
        await this.loadInitialData();
      }
    } catch {
      this.clearSession();
      this.renderUserInterface();
    }
  },

  clearSession() {
    this.state.token = '';
    this.state.user = null;
    localStorage.removeItem('procurement_token');
    localStorage.removeItem('procurement_user');
  },

  async quickLogin(email, password = 'password') {
    this.showToast('Memuat sesi akun...', 'info');
    try {
      const res = await this.api('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password })
      });

      const token = res?.result?.access_token || res?.result?.token;
      if (token && res?.result?.user) {
        this.state.token = token;
        this.state.user = res.result.user;
        localStorage.setItem('procurement_token', token);
        localStorage.setItem('procurement_user', JSON.stringify(res.result.user));

        this.showToast(`Berhasil masuk sebagai ${res.result.user.name} (${res.result.user.role})`, 'success');
        this.renderUserInterface();
        await this.loadInitialData();
      }
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async logout() {
    try {
      await this.api('/auth/logout', { method: 'POST' });
    } catch {
      // Abaikan kegagalan jaringan saat sesi diakhiri lokal
    } finally {
      this.clearSession();
      this.showToast('Sesi telah diakhiri.', 'info');
      this.renderUserInterface();
      this.switchView('pipeline');
    }
  },

  async loadInitialData() {
    try {
      const [deptRes, vendorRes] = await Promise.all([
        this.api('/departments').catch(() => null),
        this.api('/vendors').catch(() => null)
      ]);

      if (deptRes) this.state.departments = this.extractList(deptRes);
      if (vendorRes) this.state.vendors = this.extractList(vendorRes);

      await this.refreshCurrentView();
    } catch (err) {
      console.error('Gagal memuat data master:', err);
    }
  },

  switchView(viewName) {
    this.state.activeView = viewName;

    document.querySelectorAll('[data-view-nav]').forEach(el => {
      const isTarget = el.getAttribute('data-view-nav') === viewName;
      if (isTarget) {
        el.classList.add('bg-slate-800', 'text-white', 'dark:bg-slate-700');
        el.classList.remove('text-slate-300', 'hover:bg-slate-800/60');
      } else {
        el.classList.remove('bg-slate-800', 'text-white', 'dark:bg-slate-700');
        el.classList.add('text-slate-300', 'hover:bg-slate-800/60');
      }
    });

    this.refreshCurrentView();
  },

  async refreshCurrentView() {
    const container = document.getElementById('main-content-area');
    if (!container) return;

    switch (this.state.activeView) {
      case 'pipeline':
        await this.renderPipelineView(container);
        break;
      case 'requests':
        await this.renderRequestsView(container);
        break;
      case 'procures':
        await this.renderProcuresView(container);
        break;
      case 'stocks':
        await this.renderStocksView(container);
        break;
      case 'vendors':
        await this.renderVendorsView(container);
        break;
      case 'reports':
        await this.renderReportsView(container);
        break;
      case 'users':
        await this.renderUsersView(container);
        break;
      case 'departments':
        await this.renderDepartmentsView(container);
        break;
      default:
        await this.renderPipelineView(container);
    }
  },

  renderUserInterface() {
    const user = this.state.user;
    const userBox = document.getElementById('active-user-badge');
    const roleBadge = document.getElementById('active-user-role');
    const deptBadge = document.getElementById('active-user-dept');
    const quickSelect = document.getElementById('role-quick-switcher');

    if (userBox && user) {
      userBox.textContent = user.name;
      if (roleBadge) {
        roleBadge.textContent = user.role.toUpperCase();
        roleBadge.className = 'px-2 py-0.5 text-xs font-semibold rounded bg-emerald-800 text-emerald-100';
      }
      if (deptBadge) {
        deptBadge.textContent = user.department?.name || 'Departemen ' + (user.department_id || '');
      }
      if (quickSelect) {
        quickSelect.value = user.email;
      }
    }

    this.updateNavigationVisibility();
  },

  updateNavigationVisibility() {
    const role = this.state.user?.role || 'guest';
    const isManagerOrAdmin = role === 'manager' || role === 'admin';
    const isPurchasingOrAdmin = role === 'purchasing' || role === 'admin';
    const isAdmin = role === 'admin';

    const navProcures = document.getElementById('nav-item-procures');
    const navReports = document.getElementById('nav-item-reports');
    const navUsers = document.getElementById('nav-item-users');
    const navDept = document.getElementById('nav-item-departments');

    if (navProcures) navProcures.style.display = isPurchasingOrAdmin ? 'flex' : 'none';
    if (navReports) navReports.style.display = isManagerOrAdmin ? 'flex' : 'none';
    if (navUsers) navUsers.style.display = isAdmin ? 'flex' : 'none';
    if (navDept) navDept.style.display = isAdmin ? 'flex' : 'none';
  },

  async renderPipelineView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-700 pb-4">
            <div>
              <h1 class="text-xl font-bold text-slate-900 dark:text-white">Peta Alur Siklus Pengadaan (Procurement Lifecycle)</h1>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Panduan langkah demi langkah proses pengadaan barang dari permohonan hingga barang diterima di gudang.</p>
            </div>
            <div class="flex items-center gap-2">
              <button onclick="ERP.openCreateRequestModal()" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg text-sm flex items-center gap-2 min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Pengajuan Baru
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="p-4 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tahap 1</span>
                <span class="badge-status badge-draft">Draft & Ajukan</span>
              </div>
              <h2 class="text-base font-semibold text-slate-900 dark:text-white mt-2">Permohonan Unit</h2>
              <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">Staf divisi memasukkan item dan estimasi harga, lalu mengirimkan (submit) permohonan.</p>
              <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 text-xs text-slate-500 dark:text-slate-400">
                Aktor: <strong>Staf Pemohon (Employee)</strong>
              </div>
            </div>

            <div class="p-4 rounded-lg border border-amber-200 dark:border-amber-900/60 bg-amber-50/50 dark:bg-amber-950/20">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">Tahap 2</span>
                <span class="badge-status badge-submitted">Review</span>
              </div>
              <h2 class="text-base font-semibold text-slate-900 dark:text-white mt-2">Persetujuan Manajer</h2>
              <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">Kepala divisi memeriksa kelayakan anggaran lalu menyetujui (Approve) atau menolak (Reject).</p>
              <div class="mt-4 pt-3 border-t border-amber-200 dark:border-amber-900/60 text-xs text-slate-500 dark:text-slate-400">
                Aktor: <strong>Manajer Divisi (Manager)</strong>
              </div>
            </div>

            <div class="p-4 rounded-lg border border-blue-200 dark:border-blue-900/60 bg-blue-50/50 dark:bg-blue-950/20">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-400">Tahap 3</span>
                <span class="badge-status badge-procurement">PO Terbit</span>
              </div>
              <h2 class="text-base font-semibold text-slate-900 dark:text-white mt-2">Penerbitan Kontrak PO</h2>
              <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">Tim pengadaan memilih rekanan (vendor), menetapkan total biaya PO, dan tanggal estimasi tiba.</p>
              <div class="mt-4 pt-3 border-t border-blue-200 dark:border-blue-900/60 text-xs text-slate-500 dark:text-slate-400">
                Aktor: <strong>Tim Pengadaan (Purchasing)</strong>
              </div>
            </div>

            <div class="p-4 rounded-lg border border-emerald-200 dark:border-emerald-900/60 bg-emerald-50/50 dark:bg-emerald-950/20">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Tahap 4</span>
                <span class="badge-status badge-completed">Selesai</span>
              </div>
              <h2 class="text-base font-semibold text-slate-900 dark:text-white mt-2">Penerimaan & Gudang</h2>
              <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">Barang tiba dari rekanan, dicek oleh logistik, status PO ditutup dan pengadaan rampung.</p>
              <div class="mt-4 pt-3 border-t border-emerald-200 dark:border-emerald-900/60 text-xs text-slate-500 dark:text-slate-400">
                Aktor: <strong>Logistik & Purchasing</strong>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-base font-bold text-slate-900 dark:text-white">Pengajuan Pengadaan Terkini</h2>
              <button onclick="ERP.switchView('requests')" class="text-xs text-emerald-700 dark:text-emerald-400 hover:underline font-semibold">Lihat Semua Data</button>
            </div>
            <div id="pipeline-recent-requests" class="overflow-hidden">
              <div class="text-center py-8 text-sm text-slate-500">Memuat data terkini...</div>
            </div>
          </div>

          <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-2">Simulasi Ganti Peran (Role Switcher)</h2>
            <p class="text-xs text-slate-600 dark:text-slate-400 mb-4">Pilih peran untuk menguji batasan hak akses (Segregation of Duties) sesuai alur ERP:</p>
            <div class="space-y-2">
              ${this.demoAccounts.map(acc => `
                <button onclick="ERP.quickLogin('${acc.email}', 'password')" class="w-full text-left p-3 rounded-lg border border-slate-200 dark:border-slate-700 hover:border-emerald-600 dark:hover:border-emerald-500 transition-colors ${this.state.user?.email === acc.email ? 'bg-emerald-50/70 border-emerald-600 dark:bg-emerald-950/30' : 'bg-slate-50/60 dark:bg-slate-900/40'} min-h-[44px]">
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-900 dark:text-white">${acc.label}</span>
                    <span class="text-2xs uppercase px-1.5 py-0.5 rounded ${this.state.user?.email === acc.email ? 'bg-emerald-700 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300'} font-semibold">${acc.role}</span>
                  </div>
                  <p class="text-2xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">${acc.desc}</p>
                </button>
              `).join('')}
            </div>
          </div>
        </div>
      </div>
    `;

    try {
      const res = await this.api('/requests');
      const requests = this.extractList(res);
      this.state.requests = requests;
      const recentContainer = document.getElementById('pipeline-recent-requests');
      if (recentContainer) {
        if (requests.length === 0) {
          recentContainer.innerHTML = `
            <div class="text-center py-8 text-slate-500 text-sm">
              Belum ada permohonan pengadaan. Klik "Buat Pengajuan Baru" untuk memulai.
            </div>
          `;
        } else {
          recentContainer.innerHTML = `
            <div class="table-responsive">
              <table class="w-full text-left border-collapse text-xs">
                <thead>
                  <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                    <th class="py-2.5 px-3 font-semibold">Nomor Request</th>
                    <th class="py-2.5 px-3 font-semibold">Divisi</th>
                    <th class="py-2.5 px-3 font-semibold">Pemohon</th>
                    <th class="py-2.5 px-3 font-semibold">Status</th>
                    <th class="py-2.5 px-3 font-semibold text-right">Tindakan</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                  ${requests.slice(0, 5).map(req => `
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                      <td class="py-3 px-3 font-mono font-medium text-slate-900 dark:text-white">${req.request_number}</td>
                      <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${req.department?.name || 'Umum'}</td>
                      <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${req.requester?.name || req.user?.name || '-'}</td>
                      <td class="py-3 px-3">${this.formatStatusBadge(req.status)}</td>
                      <td class="py-3 px-3 text-right">
                        <button onclick="ERP.viewRequestDetail('${req.id}')" class="px-2.5 py-1 text-xs font-semibold text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 rounded min-h-[36px]">
                          Rincian
                        </button>
                      </td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          `;
        }
      }
    } catch (err) {
      const recentContainer = document.getElementById('pipeline-recent-requests');
      if (recentContainer) {
        recentContainer.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
      }
    }
  },

  async renderRequestsView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div>
              <h1 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Pengajuan Pengadaan Barang</h1>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Seluruh data permohonan pengadaan barang unit kerja beserta status verifikasinya.</p>
            </div>
            <button onclick="ERP.openCreateRequestModal()" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg text-sm flex items-center gap-2 self-start sm:self-auto min-h-[44px]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              Buat Pengajuan Baru
            </button>
          </div>

          <div class="flex flex-wrap items-center gap-2 mt-4 pt-2">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 mr-2">Filter Status:</span>
            <button onclick="ERP.filterRequests('ALL')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-800 text-white dark:bg-slate-600 min-h-[36px]" data-filter="ALL">Semua</button>
            <button onclick="ERP.filterRequests('DRAFT')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]" data-filter="DRAFT">Draft</button>
            <button onclick="ERP.filterRequests('SUBMITTED')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]" data-filter="SUBMITTED">Menunggu Review</button>
            <button onclick="ERP.filterRequests('APPROVED')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]" data-filter="APPROVED">Disetujui</button>
            <button onclick="ERP.filterRequests('IN_PROCUREMENT')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]" data-filter="IN_PROCUREMENT">Proses PO</button>
            <button onclick="ERP.filterRequests('COMPLETED')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]" data-filter="COMPLETED">Selesai</button>
            <button onclick="ERP.filterRequests('REJECTED')" class="req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]" data-filter="REJECTED">Ditolak</button>
          </div>
        </div>

        <div id="requests-table-container" class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
          <div class="text-center py-12 text-sm text-slate-500">Memuat data pengadaan...</div>
        </div>
      </div>
    `;

    await this.fetchAndRenderRequestsTable();
  },

  async fetchAndRenderRequestsTable(filter = 'ALL') {
    const container = document.getElementById('requests-table-container');
    if (!container) return;

    try {
      const res = await this.api('/requests');
      let requests = this.extractList(res);
      this.state.requests = requests;

      if (filter !== 'ALL') {
        requests = requests.filter(r => r.status === filter);
      }

      if (requests.length === 0) {
        container.innerHTML = `
          <div class="text-center py-12">
            <p class="text-sm text-slate-600 dark:text-slate-400">Tidak ada pengajuan pengadaan dengan filter ini.</p>
            <button onclick="ERP.openCreateRequestModal()" class="mt-4 px-3 py-1.5 bg-emerald-700 text-white rounded text-xs font-semibold min-h-[44px]">Buat Pengajuan Sekarang</button>
          </div>
        `;
        return;
      }

      const role = this.state.user?.role || 'guest';
      const isManagerOrAdmin = role === 'manager' || role === 'admin';
      const isPurchasingOrAdmin = role === 'purchasing' || role === 'admin';

      container.innerHTML = `
        <div class="table-responsive">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <th class="py-3 px-3 font-semibold">Nomor REQ</th>
                <th class="py-3 px-3 font-semibold">Departemen</th>
                <th class="py-3 px-3 font-semibold">Pemohon</th>
                <th class="py-3 px-3 font-semibold">Jumlah Item</th>
                <th class="py-3 px-3 font-semibold">Status</th>
                <th class="py-3 px-3 font-semibold">Tanggal Buat</th>
                <th class="py-3 px-3 font-semibold text-right">Tindakan Workflow</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
              ${requests.map(req => {
                const itemCount = req.items?.length || (req.request_items?.length || 0);
                const canSubmit = req.status === 'DRAFT';
                const canApprove = req.status === 'SUBMITTED' && isManagerOrAdmin;
                const canProcure = req.status === 'APPROVED' && isPurchasingOrAdmin;
                const canComplete = req.status === 'IN_PROCUREMENT' && isPurchasingOrAdmin;

                return `
                  <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                    <td class="py-3 px-3 font-mono font-medium text-slate-900 dark:text-white">${req.request_number}</td>
                    <td class="py-3 px-3 text-slate-700 dark:text-slate-300">${req.department?.name || '-'}</td>
                    <td class="py-3 px-3 text-slate-700 dark:text-slate-300">${req.requester?.name || req.user?.name || '-'}</td>
                    <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-300">${itemCount} item</td>
                    <td class="py-3 px-3">${this.formatStatusBadge(req.status)}</td>
                    <td class="py-3 px-3 text-slate-500 dark:text-slate-400">${this.formatDate(req.created_at)}</td>
                    <td class="py-3 px-3 text-right">
                      <div class="inline-flex items-center gap-1.5 flex-wrap justify-end">
                        <button onclick="ERP.viewRequestDetail('${req.id}')" class="px-2 py-1 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 min-h-[36px]" title="Lihat Rincian">
                          Rincian
                        </button>
                        ${canSubmit ? `
                          <button onclick="ERP.submitRequest('${req.id}')" class="px-2 py-1 text-xs font-semibold rounded bg-amber-600 hover:bg-amber-700 text-white min-h-[36px]" title="Kirim permohonan ke manajer">
                            Submit
                          </button>
                        ` : ''}
                        ${canApprove ? `
                          <button onclick="ERP.approveRequest('${req.id}')" class="px-2 py-1 text-xs font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white min-h-[36px]" title="Setujui permohonan">
                            Setujui
                          </button>
                          <button onclick="ERP.openRejectModal('${req.id}')" class="px-2 py-1 text-xs font-semibold rounded bg-rose-700 hover:bg-rose-800 text-white min-h-[36px]" title="Tolak permohonan">
                            Tolak
                          </button>
                        ` : ''}
                        ${canProcure ? `
                          <button onclick="ERP.openProcureModal('${req.id}')" class="px-2 py-1 text-xs font-semibold rounded bg-blue-700 hover:bg-blue-800 text-white min-h-[36px]" title="Terbitkan PO ke Vendor">
                            Terbitkan PO
                          </button>
                        ` : ''}
                        ${canComplete ? `
                          <button onclick="ERP.completeDirect('${req.id}')" class="px-2 py-1 text-xs font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white min-h-[36px]" title="Selesaikan Pengadaan">
                            Selesaikan
                          </button>
                        ` : ''}
                      </div>
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      `;
    } catch (err) {
      container.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
    }
  },

  filterRequests(status) {
    document.querySelectorAll('.req-filter-btn').forEach(btn => {
      const match = btn.getAttribute('data-filter') === status;
      if (match) {
        btn.className = 'req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-800 text-white dark:bg-slate-600 min-h-[36px]';
      } else {
        btn.className = 'req-filter-btn px-2.5 py-1 text-xs rounded font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 min-h-[36px]';
      }
    });

    this.fetchAndRenderRequestsTable(status);
  },

  async submitRequest(requestId) {
    if (!confirm('Apakah Anda yakin ingin mengirim permohonan ini untuk direview oleh manajer?')) return;
    try {
      await this.api(`/requests/${requestId}/submit`, { method: 'PUT' });
      this.showToast('Permohonan berhasil dikirim (Status: SUBMITTED).', 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async approveRequest(requestId) {
    if (!confirm('Setujui permohonan pengadaan ini?')) return;
    try {
      await this.api(`/requests/${requestId}/approve`, {
        method: 'PUT',
        body: JSON.stringify({ notes: 'Disetujui oleh manajer divisi.' })
      });
      this.showToast('Permohonan berhasil disetujui (Status: APPROVED).', 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  openRejectModal(requestId) {
    const modal = document.getElementById('erp-modal');
    const modalContent = document.getElementById('erp-modal-content');
    if (!modal || !modalContent) return;

    modalContent.innerHTML = `
      <div class="p-6">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Alasan Penolakan Permohonan</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sesuai aturan tata kelola, penolakan pengadaan wajib menyertakan catatan tertulis untuk pemohon.</p>
        <div class="mt-4">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Alasan Penolakan:</label>
          <textarea id="reject-notes-input" rows="3" class="w-full text-xs p-2.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" placeholder="Contoh: Anggaran kuartal ini belum mencukupi atau spesifikasi barang tidak sesuai kebutuhan..."></textarea>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <button onclick="ERP.closeModal()" class="px-3 py-1.5 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 min-h-[44px]">Batal</button>
          <button onclick="ERP.submitReject('${requestId}')" class="px-3 py-1.5 text-xs font-semibold rounded bg-rose-700 hover:bg-rose-800 text-white min-h-[44px]">Tolak Pengadaan</button>
        </div>
      </div>
    `;

    modal.classList.remove('hidden');
    document.getElementById('reject-notes-input')?.focus();
  },

  async submitReject(requestId) {
    const reason = document.getElementById('reject-notes-input')?.value.trim();
    if (!reason) {
      alert('Wajib mengisi catatan alasan penolakan.');
      return;
    }

    try {
      await this.api(`/requests/${requestId}/reject`, {
        method: 'PUT',
        body: JSON.stringify({ reason: reason, notes: reason })
      });
      this.closeModal();
      this.showToast('Permohonan telah ditolak (Status: REJECTED).', 'info');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  openProcureModal(requestId) {
    const modal = document.getElementById('erp-modal');
    const modalContent = document.getElementById('erp-modal-content');
    if (!modal || !modalContent) return;

    modalContent.innerHTML = `
      <div class="p-6">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Terbitkan Purchase Order (PO)</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pilih mitra vendor rekanan dan tentukan nilai kontrak PO serta tanggal pengiriman.</p>
        
        <form onsubmit="event.preventDefault(); ERP.submitProcure('${requestId}');" class="mt-4 space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Pilih Rekanan / Vendor:</label>
            <select id="procure-vendor-id" required class="w-full text-xs p-2.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
              <option value="">Pilih Vendor Rekanan</option>
              ${this.state.vendors.map(v => `<option value="${v.id}">${v.name} (${v.code} - ${v.category})</option>`).join('')}
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Total Biaya PO (Rupiah):</label>
            <input type="number" id="procure-total-amount" min="1000" step="1000" required placeholder="Contoh: 15000000" class="w-full text-xs p-2.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Estimasi Tanggal Tiba (Delivery Date):</label>
            <input type="date" id="procure-delivery-date" required class="w-full text-xs p-2.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Catatan Tambahan (Opsional):</label>
            <input type="text" id="procure-notes" placeholder="Catatan syarat pengiriman atau garansi" class="w-full text-xs p-2.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
          </div>

          <div class="mt-6 flex justify-end gap-2 pt-2">
            <button type="button" onclick="ERP.closeModal()" class="px-3 py-1.5 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 min-h-[44px]">Batal</button>
            <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded bg-blue-700 hover:bg-blue-800 text-white min-h-[44px]">Terbitkan Kontrak PO</button>
          </div>
        </form>
      </div>
    `;

    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const dateInput = document.getElementById('procure-delivery-date');
    if (dateInput) {
      dateInput.min = tomorrow.toISOString().split('T')[0];
      dateInput.value = tomorrow.toISOString().split('T')[0];
    }

    modal.classList.remove('hidden');
  },

  async submitProcure(requestId) {
    const vendor_id = document.getElementById('procure-vendor-id')?.value;
    const total_amount = document.getElementById('procure-total-amount')?.value;
    const expected_delivery_date = document.getElementById('procure-delivery-date')?.value;
    const notes = document.getElementById('procure-notes')?.value.trim();

    if (!vendor_id || !total_amount || !expected_delivery_date) {
      alert('Harap lengkapi seluruh field formulir PO.');
      return;
    }

    try {
      await this.api(`/requests/${requestId}/procure`, {
        method: 'PUT',
        body: JSON.stringify({
          vendor_id,
          total_amount: Number(total_amount),
          expected_delivery_date,
          notes: notes || undefined
        })
      });

      this.closeModal();
      this.showToast('Purchase Order berhasil diterbitkan ke vendor.', 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async completeDirect(requestId) {
    if (!confirm('Selesaikan permohonan pengadaan ini?')) return;
    try {
      await this.api(`/requests/${requestId}/complete`, { method: 'PUT' });
      this.showToast('Pengadaan berhasil diselesaikan (Status: COMPLETED).', 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  openCreateRequestModal() {
    const modal = document.getElementById('erp-modal');
    const modalContent = document.getElementById('erp-modal-content');
    if (!modal || !modalContent) return;

    modalContent.innerHTML = `
      <div class="p-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
          <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Formulir Pengajuan Pengadaan Barang</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Tambahkan barang yang dibutuhkan unit kerja Anda.</p>
          </div>
          <button onclick="ERP.closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 min-h-[44px] min-w-[44px] flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <form id="create-request-form" onsubmit="event.preventDefault(); ERP.submitCreateRequest();" class="mt-4 space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Alasan / Catatan Pengajuan:</label>
            <input type="text" id="req-notes" required placeholder="Contoh: Kebutuhan perlengkapan operasional kuartal 2" class="w-full text-xs p-2.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
          </div>

          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-semibold text-slate-700 dark:text-slate-300">Rincian Barang yang Diminta:</label>
              <button type="button" onclick="ERP.addRequestItemRow()" class="px-2.5 py-1 text-xs font-semibold rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-200 min-h-[36px]">
                + Tambah Baris Barang
              </button>
            </div>
            
            <div id="request-items-container" class="space-y-2 max-h-64 overflow-y-auto pr-1">
              <!-- Item rows injected here -->
            </div>
          </div>

          <div class="mt-6 flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700">
            <div class="text-xs text-slate-500">Permohonan akan disimpan sebagai <strong class="text-amber-600">Draft</strong>.</div>
            <div class="flex gap-2">
              <button type="button" onclick="ERP.closeModal()" class="px-3 py-1.5 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 min-h-[44px]">Batal</button>
              <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white min-h-[44px]">Simpan Pengajuan</button>
            </div>
          </div>
        </form>
      </div>
    `;

    this.addRequestItemRow();
    modal.classList.remove('hidden');
  },

  addRequestItemRow() {
    const container = document.getElementById('request-items-container');
    if (!container) return;

    const rowId = 'item-row-' + Date.now() + '-' + Math.floor(Math.random() * 100);
    const row = document.createElement('div');
    row.id = rowId;
    row.className = 'grid grid-cols-12 gap-2 p-2.5 bg-slate-50 dark:bg-slate-900/50 rounded border border-slate-200 dark:border-slate-700 items-end';

    row.innerHTML = `
      <div class="col-span-3">
        <label class="block text-2xs font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Nama Barang:</label>
        <input type="text" class="item-name w-full text-xs p-1.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white" required placeholder="Contoh: Kertas A4">
      </div>
      <div class="col-span-3">
        <label class="block text-2xs font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Kategori:</label>
        <select class="item-category w-full text-xs p-1.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white" required>
          <option value="Office Supplies">Office Supplies</option>
          <option value="Electronics">Electronics</option>
          <option value="Furniture">Furniture</option>
          <option value="Office Equipment">Office Equipment</option>
          <option value="Operational">Operational</option>
        </select>
      </div>
      <div class="col-span-2">
        <label class="block text-2xs font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Jumlah:</label>
        <input type="number" class="item-qty w-full text-xs p-1.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white" min="1" value="1" required>
      </div>
      <div class="col-span-1">
        <label class="block text-2xs font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Satuan:</label>
        <input type="text" class="item-unit w-full text-xs p-1.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white" placeholder="pcs" value="pcs" required>
      </div>
      <div class="col-span-2">
        <label class="block text-2xs font-semibold text-slate-600 dark:text-slate-400 mb-0.5">Estimasi (Rp):</label>
        <input type="number" class="item-price w-full text-xs p-1.5 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white" min="0" step="500" placeholder="50000" value="50000">
      </div>
      <div class="col-span-1 flex justify-center pb-0.5">
        <button type="button" onclick="document.getElementById('${rowId}')?.remove()" class="p-1 text-slate-400 hover:text-rose-600 min-h-[36px] min-w-[36px] flex items-center justify-center" title="Hapus baris">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
      </div>
    `;

    container.appendChild(row);
  },

  async submitCreateRequest() {
    const notes = document.getElementById('req-notes')?.value.trim();
    const rows = document.querySelectorAll('#request-items-container > div');

    if (rows.length === 0) {
      alert('Wajib menambahkan minimal 1 item barang.');
      return;
    }

    const items = [];
    rows.forEach(r => {
      const name = r.querySelector('.item-name')?.value.trim();
      const category = r.querySelector('.item-category')?.value.trim() || 'Office Supplies';
      const qty = parseInt(r.querySelector('.item-qty')?.value, 10);
      const unit = r.querySelector('.item-unit')?.value.trim();
      const price = parseFloat(r.querySelector('.item-price')?.value || '0');

      if (name && qty > 0) {
        items.push({
          item_name: name,
          category: category,
          quantity: qty,
          unit: unit || 'unit',
          estimated_price: price,
          notes: ''
        });
      }
    });

    if (items.length === 0) {
      alert('Data barang belum lengkap.');
      return;
    }

    try {
      const res = await this.api('/requests', {
        method: 'POST',
        body: JSON.stringify({
          notes,
          items
        })
      });

      this.closeModal();
      this.showToast(`Pengajuan ${res?.result?.request_number || ''} berhasil dibuat sebagai DRAFT.`, 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async viewRequestDetail(requestId) {
    const modal = document.getElementById('erp-modal');
    const modalContent = document.getElementById('erp-modal-content');
    if (!modal || !modalContent) return;

    modalContent.innerHTML = `<div class="p-8 text-center text-sm text-slate-500">Memuat rincian data...</div>`;
    modal.classList.remove('hidden');

    try {
      const res = await this.api(`/requests`);
      const allRequests = this.extractList(res);
      const req = allRequests.find(r => r.id === requestId);

      if (!req) {
        throw new Error('Data pengadaan tidak ditemukan.');
      }

      const items = req.items || req.request_items || [];
      const histories = req.status_histories || [];

      modalContent.innerHTML = `
        <div class="p-6">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
            <div>
              <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-slate-900 dark:text-white font-mono">${req.request_number}</h2>
                ${this.formatStatusBadge(req.status)}
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Diajukan oleh ${req.requester?.name || req.user?.name || '-'} (${req.department?.name || '-'}) pada ${this.formatDate(req.created_at)}</p>
            </div>
            <button onclick="ERP.closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 min-h-[44px] min-w-[44px] flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>

          <div class="mt-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Daftar Barang yang Diminta</h3>
            <div class="table-responsive border border-slate-200 dark:border-slate-700 rounded">
              <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                  <tr>
                    <th class="py-2 px-3 font-semibold">Nama Barang</th>
                    <th class="py-2 px-3 font-semibold">Kategori</th>
                    <th class="py-2 px-3 font-semibold">Jumlah</th>
                    <th class="py-2 px-3 font-semibold">Satuan</th>
                    <th class="py-2 px-3 font-semibold text-right">Estimasi Harga</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                  ${items.length === 0 ? '<tr><td colspan="5" class="p-3 text-center text-slate-400">Tidak ada rincian item</td></tr>' : items.map(it => `
                    <tr>
                      <td class="py-2 px-3 text-slate-900 dark:text-white font-medium">${it.item_name}</td>
                      <td class="py-2 px-3 text-slate-600 dark:text-slate-400">${it.category || '-'}</td>
                      <td class="py-2 px-3 text-slate-700 dark:text-slate-300">${it.quantity}</td>
                      <td class="py-2 px-3 text-slate-700 dark:text-slate-300">${it.unit}</td>
                      <td class="py-2 px-3 text-right font-mono text-slate-700 dark:text-slate-300">${this.formatRupiah(it.estimated_price)}</td>
                    </tr>
                  `).join('')}
                </tbody>
              </table>
            </div>
          </div>

          <div class="mt-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">Riwayat Jejak Audit Perubahan Status (Audit Trail)</h3>
            <div class="space-y-2 max-h-44 overflow-y-auto pr-1">
              ${histories.length === 0 ? '<div class="text-xs text-slate-400 py-2">Belum ada riwayat transisi status lanjutan.</div>' : histories.map(h => `
                <div class="p-2.5 rounded bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-xs">
                  <div class="flex items-center justify-between">
                    <span class="font-semibold text-slate-800 dark:text-slate-200">${h.from_status || 'AWAL'} ➔ <span class="text-emerald-700 dark:text-emerald-400">${h.to_status}</span></span>
                    <span class="text-2xs text-slate-400">${this.formatDate(h.created_at)}</span>
                  </div>
                  <div class="text-slate-600 dark:text-slate-400 mt-1">Oleh: ${h.changed_by_user?.name || h.changed_by || 'Sistem'} ${h.notes ? `| Catatan: "${h.notes}"` : ''}</div>
                  <div class="text-2xs font-mono text-slate-400 mt-0.5">IP: ${h.ip_address || '-'} | UA: ${h.user_agent ? h.user_agent.substring(0, 40) + '...' : '-'}</div>
                </div>
              `).join('')}
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button onclick="ERP.closeModal()" class="px-4 py-1.5 text-xs font-semibold rounded bg-slate-800 hover:bg-slate-900 text-white dark:bg-slate-700 dark:hover:bg-slate-600 min-h-[44px]">Tutup</button>
          </div>
        </div>
      `;
    } catch (err) {
      modalContent.innerHTML = `<div class="p-6 text-xs text-rose-600">${err.message}</div>`;
    }
  },

  async renderProcuresView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div>
              <h1 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Kontrak & Pesanan Pembelian (Purchase Orders)</h1>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Monitoring status penerbitan PO ke vendor rekanan dan konfirmasi penerimaan barang.</p>
            </div>
          </div>
        </div>

        <div id="procures-table-container" class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
          <div class="text-center py-12 text-sm text-slate-500">Memuat data pesanan pembelian...</div>
        </div>
      </div>
    `;

    try {
      const res = await this.api('/procures');
      const orders = this.extractList(res);
      this.state.procures = orders;

      const tableContainer = document.getElementById('procures-table-container');
      if (!tableContainer) return;

      if (orders.length === 0) {
        tableContainer.innerHTML = `
          <div class="text-center py-12 text-sm text-slate-500">
            Belum ada Purchase Order yang diterbitkan. Anda dapat menerbitkan PO dari permohonan yang berstatus "APPROVED".
          </div>
        `;
        return;
      }

      tableContainer.innerHTML = `
        <div class="table-responsive">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <th class="py-3 px-3 font-semibold">Nomor PO</th>
                <th class="py-3 px-3 font-semibold">Mitra Rekanan / Vendor</th>
                <th class="py-3 px-3 font-semibold">Nomor Request</th>
                <th class="py-3 px-3 font-semibold">Total Biaya PO</th>
                <th class="py-3 px-3 font-semibold">Estimasi Tiba</th>
                <th class="py-3 px-3 font-semibold">Status Pengiriman</th>
                <th class="py-3 px-3 font-semibold text-right">Tindakan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
              ${orders.map(po => {
                const isDelivered = po.status === 'DELIVERED';
                return `
                  <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                    <td class="py-3 px-3 font-mono font-medium text-slate-900 dark:text-white">${po.po_number}</td>
                    <td class="py-3 px-3 text-slate-700 dark:text-slate-300 font-medium">${po.vendor?.name || 'Vendor ' + (po.vendor_id || '-')}</td>
                    <td class="py-3 px-3 font-mono text-slate-600 dark:text-slate-400">${po.request?.request_number || '-'}</td>
                    <td class="py-3 px-3 font-mono font-medium text-slate-800 dark:text-slate-200">${this.formatRupiah(po.total_amount)}</td>
                    <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${this.formatDate(po.expected_delivery_date || po.estimated_delivery)}</td>
                    <td class="py-3 px-3">
                      <span class="badge-status ${isDelivered ? 'badge-completed' : 'badge-procurement'}">
                        ${po.status}
                      </span>
                    </td>
                    <td class="py-3 px-3 text-right">
                      ${!isDelivered ? `
                        <button onclick="ERP.deliverProcure('${po.id}')" class="px-2.5 py-1 text-xs font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white min-h-[36px]" title="Konfirmasi barang telah diterima di gudang">
                          Konfirmasi Tiba (Deliver)
                        </button>
                      ` : `
                        <span class="text-xs text-slate-400">Telah Diterima</span>
                      `}
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      `;
    } catch (err) {
      const tableContainer = document.getElementById('procures-table-container');
      if (tableContainer) {
        tableContainer.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
      }
    }
  },

  async deliverProcure(poId) {
    if (!confirm('Konfirmasi bahwa barang dari vendor telah tiba dan diterima dengan baik?')) return;
    try {
      await this.api(`/procures/${poId}/deliver`, { method: 'PUT' });
      this.showToast('Barang berhasil diverifikasi tiba (PO DELIVERED).', 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async renderStocksView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div>
              <h1 class="text-xl font-bold text-slate-900 dark:text-white">Inventaris Gudang & Pemeriksaan Stok Real-time</h1>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Katalog stok logistik gudang dengan mekanisme penguncian konkurensi (Pessimistic Concurrency Lock).</p>
            </div>
            ${(this.state.user?.role === 'warehouse' || this.state.user?.role === 'admin') ? `
              <button onclick="ERP.openCreateStockModal()" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg text-sm flex items-center gap-2 min-h-[44px]">
                + Tambah Master Stok
              </button>
            ` : ''}
          </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900/60 p-5 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex items-center gap-2 mb-2">
            <span class="px-2 py-0.5 text-2xs font-bold rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 uppercase">Simulasi Fitur Enterprise</span>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pemeriksaan Ketersediaan Stok Real-time (lockForUpdate)</h2>
          </div>
          <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
            Fitur ini memanggil endpoint <code>POST /api/v1/stocks/check</code>. Sistem menggunakan transaksi atomik dengan perintah SQL <code>SELECT ... FOR UPDATE</code>. Mekanisme ini mengunci baris data di basis data sehingga jika ada transaksi bersamaan, stok tidak dapat teralokasi ganda (race condition prevention).
          </p>

          <form onsubmit="event.preventDefault(); ERP.simulateStockCheck();" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-5">
              <label class="block text-2xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Barang yang Diperiksa:</label>
              <input type="text" id="check-stock-name" required placeholder="Contoh: Kertas A4" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
            <div class="md:col-span-3">
              <label class="block text-2xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Dibutuhkan:</label>
              <input type="number" id="check-stock-qty" min="1" value="10" required class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white">
            </div>
            <div class="md:col-span-4">
              <button type="submit" class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-semibold min-h-[44px]">
                Uji Pengecekan Ketersediaan
              </button>
            </div>
          </form>

          <div id="check-stock-result" class="hidden mt-3 p-3 rounded text-xs"></div>
        </div>

        <div id="stocks-table-container" class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
          <div class="text-center py-12 text-sm text-slate-500">Memuat katalog stok...</div>
        </div>
      </div>
    `;

    await this.fetchAndRenderStocksTable();
  },

  async fetchAndRenderStocksTable() {
    const container = document.getElementById('stocks-table-container');
    if (!container) return;

    try {
      const res = await this.api('/stocks');
      const stocks = this.extractList(res);
      this.state.stocks = stocks;

      if (stocks.length === 0) {
        container.innerHTML = `<div class="text-center py-12 text-sm text-slate-500">Katalog stok gudang masih kosong.</div>`;
        return;
      }

      container.innerHTML = `
        <div class="table-responsive">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <th class="py-3 px-3 font-semibold">Nama Barang</th>
                <th class="py-3 px-3 font-semibold">Kategori</th>
                <th class="py-3 px-3 font-semibold">Jumlah Stok</th>
                <th class="py-3 px-3 font-semibold">Satuan</th>
                <th class="py-3 px-3 font-semibold">Lokasi Rak</th>
                <th class="py-3 px-3 font-semibold">Ambang Batas Minimum</th>
                <th class="py-3 px-3 font-semibold">Status Ketersediaan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
              ${stocks.map(stk => {
                const isCritical = Number(stk.quantity) <= Number(stk.minimum_stock);
                return `
                  <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                    <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">${stk.item_name}</td>
                    <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${stk.category}</td>
                    <td class="py-3 px-3 font-mono font-bold ${isCritical ? 'text-rose-600 dark:text-rose-400' : 'text-slate-800 dark:text-slate-200'}">${stk.quantity}</td>
                    <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${stk.unit}</td>
                    <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${stk.location || '-'}</td>
                    <td class="py-3 px-3 font-mono text-slate-500">${stk.minimum_stock}</td>
                    <td class="py-3 px-3">
                      ${isCritical ? `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200">
                          Stok Kritis / Menipis
                        </span>
                      ` : `
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200">
                          Aman
                        </span>
                      `}
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
      `;
    } catch (err) {
      container.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
    }
  },

  async simulateStockCheck() {
    const itemName = document.getElementById('check-stock-name')?.value.trim();
    const requiredQty = parseInt(document.getElementById('check-stock-qty')?.value, 10);
    const resultBox = document.getElementById('check-stock-result');

    if (!itemName || !requiredQty || !resultBox) return;

    resultBox.classList.remove('hidden', 'bg-emerald-50', 'bg-rose-50', 'text-emerald-800', 'text-rose-800', 'dark:bg-emerald-950', 'dark:bg-rose-950');
    resultBox.innerHTML = 'Memeriksa ketersediaan dengan concurrency lock...';

    try {
      const res = await this.api('/stocks/check', {
        method: 'POST',
        body: JSON.stringify({
          item_name: itemName,
          required_qty: requiredQty
        })
      });

      const isAvail = res?.result?.available;
      if (isAvail) {
        resultBox.className = 'mt-3 p-3 rounded text-xs bg-emerald-50 text-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800';
        resultBox.innerHTML = `<strong>Ketersediaan Terverifikasi:</strong> Stok barang "${itemName}" mencukupi (Tersedia: ${res.result.available_qty}, Dibutuhkan: ${requiredQty}). Mekanisme lockForUpdate berhasil mengunci baris stok.`;
      } else {
        resultBox.className = 'mt-3 p-3 rounded text-xs bg-amber-50 text-amber-900 dark:bg-amber-950/50 dark:text-amber-200 border border-amber-200 dark:border-amber-800';
        resultBox.innerHTML = `<strong>Stok Tidak Mencukupi:</strong> Stok barang "${itemName}" tidak memenuhi jumlah (Tersedia: ${res?.result?.available_qty || 0}, Dibutuhkan: ${requiredQty}). Permohonan pengadaan ke vendor disarankan.`;
      }
    } catch (err) {
      resultBox.className = 'mt-3 p-3 rounded text-xs bg-rose-50 text-rose-900 dark:bg-rose-950/50 dark:text-rose-200 border border-rose-200 dark:border-rose-800';
      resultBox.innerHTML = `<strong>Hasil Pengecekan:</strong> ${err.message}`;
    }
  },

  openCreateStockModal() {
    const modal = document.getElementById('erp-modal');
    const modalContent = document.getElementById('erp-modal-content');
    if (!modal || !modalContent) return;

    modalContent.innerHTML = `
      <div class="p-6">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Tambah Master Stok Gudang</h2>
        <form onsubmit="event.preventDefault(); ERP.submitCreateStock();" class="mt-4 space-y-3">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Barang:</label>
            <input type="text" id="stock-item-name" required placeholder="Contoh: Lakban Coklat 2 Inch" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori:</label>
              <input type="text" id="stock-category" required placeholder="Office Supplies" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Satuan:</label>
              <input type="text" id="stock-unit" required placeholder="roll / pcs / unit" value="pcs" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Jumlah Stok:</label>
              <input type="number" id="stock-qty" min="0" value="50" required class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Lokasi Rak:</label>
              <input type="text" id="stock-location" placeholder="Gudang A-3" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Ambang Minimum:</label>
              <input type="number" id="stock-min" min="0" value="10" required class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-2 pt-2">
            <button type="button" onclick="ERP.closeModal()" class="px-3 py-1.5 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 min-h-[44px]">Batal</button>
            <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white min-h-[44px]">Simpan Stok</button>
          </div>
        </form>
      </div>
    `;

    modal.classList.remove('hidden');
  },

  async submitCreateStock() {
    const item_name = document.getElementById('stock-item-name')?.value.trim();
    const category = document.getElementById('stock-category')?.value.trim();
    const unit = document.getElementById('stock-unit')?.value.trim();
    const quantity = parseInt(document.getElementById('stock-qty')?.value, 10);
    const location = document.getElementById('stock-location')?.value.trim();
    const minimum_stock = parseInt(document.getElementById('stock-min')?.value, 10);

    try {
      await this.api('/stocks', {
        method: 'POST',
        body: JSON.stringify({ item_name, category, unit, quantity, location, minimum_stock })
      });

      this.closeModal();
      this.showToast('Data stok berhasil ditambahkan ke inventaris.', 'success');
      await this.fetchAndRenderStocksTable();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async renderVendorsView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div>
              <h1 class="text-xl font-bold text-slate-900 dark:text-white">Daftar Rekanan & Vendor Terverifikasi</h1>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Katalog mitra penyedia barang dan jasa yang terdaftar dalam sistem pengadaan.</p>
            </div>
            ${(this.state.user?.role === 'purchasing' || this.state.user?.role === 'admin') ? `
              <button onclick="ERP.openCreateVendorModal()" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-medium rounded-lg text-sm flex items-center gap-2 min-h-[44px]">
                + Daftarkan Rekanan
              </button>
            ` : ''}
          </div>
        </div>

        <div id="vendors-table-container" class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
          <div class="text-center py-12 text-sm text-slate-500">Memuat katalog vendor...</div>
        </div>
      </div>
    `;

    try {
      const res = await this.api('/vendors');
      const vendors = this.extractList(res);
      this.state.vendors = vendors;

      const tableContainer = document.getElementById('vendors-table-container');
      if (!tableContainer) return;

      if (vendors.length === 0) {
        tableContainer.innerHTML = `<div class="text-center py-12 text-sm text-slate-500">Belum ada vendor terdaftar.</div>`;
        return;
      }

      tableContainer.innerHTML = `
        <div class="table-responsive">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <th class="py-3 px-3 font-semibold">Kode Vendor</th>
                <th class="py-3 px-3 font-semibold">Nama Perusahaan</th>
                <th class="py-3 px-3 font-semibold">Kategori Bidang</th>
                <th class="py-3 px-3 font-semibold">Kontak Person</th>
                <th class="py-3 px-3 font-semibold">Email</th>
                <th class="py-3 px-3 font-semibold">Telepon</th>
                <th class="py-3 px-3 font-semibold">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
              ${vendors.map(v => `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                  <td class="py-3 px-3 font-mono font-medium text-slate-900 dark:text-white">${v.code}</td>
                  <td class="py-3 px-3 font-bold text-slate-800 dark:text-slate-100">${v.name}</td>
                  <td class="py-3 px-3 text-slate-600 dark:text-slate-300">${v.category || '-'}</td>
                  <td class="py-3 px-3 text-slate-700 dark:text-slate-300">${v.contact_person || '-'}</td>
                  <td class="py-3 px-3 text-slate-600 dark:text-slate-400 font-mono">${v.email || '-'}</td>
                  <td class="py-3 px-3 text-slate-600 dark:text-slate-400">${v.phone || '-'}</td>
                  <td class="py-3 px-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold ${v.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' : 'bg-slate-200 text-slate-600'}">
                      ${v.is_active ? 'Aktif' : 'Non-aktif'}
                    </span>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
    } catch (err) {
      const tableContainer = document.getElementById('vendors-table-container');
      if (tableContainer) {
        tableContainer.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
      }
    }
  },

  openCreateVendorModal() {
    const modal = document.getElementById('erp-modal');
    const modalContent = document.getElementById('erp-modal-content');
    if (!modal || !modalContent) return;

    modalContent.innerHTML = `
      <div class="p-6">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftarkan Rekanan / Vendor Baru</h2>
        <form onsubmit="event.preventDefault(); ERP.submitCreateVendor();" class="mt-4 space-y-3">
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kode Vendor:</label>
              <input type="text" id="vendor-code" required placeholder="VND-005" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white font-mono">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kategori:</label>
              <input type="text" id="vendor-cat" required placeholder="Office Supplies" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Perusahaan Rekanan:</label>
            <input type="text" id="vendor-name" required placeholder="PT Mitra Logistik Nusantara" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
          </div>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Kontak Person:</label>
              <input type="text" id="vendor-pic" placeholder="Nama PIC" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Email:</label>
              <input type="email" id="vendor-email" placeholder="pic@perusahaan.co.id" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Telepon:</label>
              <input type="text" id="vendor-phone" placeholder="021-xxxx" class="w-full text-xs p-2 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white">
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-2 pt-2">
            <button type="button" onclick="ERP.closeModal()" class="px-3 py-1.5 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 min-h-[44px]">Batal</button>
            <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white min-h-[44px]">Simpan Rekanan</button>
          </div>
        </form>
      </div>
    `;

    modal.classList.remove('hidden');
  },

  async submitCreateVendor() {
    const code = document.getElementById('vendor-code')?.value.trim();
    const category = document.getElementById('vendor-cat')?.value.trim();
    const name = document.getElementById('vendor-name')?.value.trim();
    const contact_person = document.getElementById('vendor-pic')?.value.trim();
    const email = document.getElementById('vendor-email')?.value.trim();
    const phone = document.getElementById('vendor-phone')?.value.trim();

    try {
      await this.api('/vendors', {
        method: 'POST',
        body: JSON.stringify({ code, category, name, contact_person, email, phone, is_active: true })
      });

      this.closeModal();
      this.showToast('Data rekanan vendor berhasil disimpan.', 'success');
      await this.refreshCurrentView();
    } catch (err) {
      this.showToast(err.message, 'error');
    }
  },

  async renderReportsView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
            <div>
              <h1 class="text-xl font-bold text-slate-900 dark:text-white">Laporan Eksekutif & Analisis Lead Time Pengadaan</h1>
              <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Metrik SLA dan agregasi data riil bersumber langsung dari kueri analitik backend.</p>
            </div>
            <button onclick="window.print()" class="no-print px-3 py-1.5 text-xs font-semibold rounded border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-100 min-h-[44px]">
              Cetak / Simpan PDF
            </button>
          </div>
        </div>

        <div id="reports-summary-cards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="p-6 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-center">Memuat metrik...</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-1">Rata-rata Waktu Siklus (Lead Time SLA)</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Waktu yang dibutuhkan dari pengajuan permohonan hingga barang diterima di gudang.</p>
            <div id="reports-lead-time-container">Memuat data SLA...</div>
          </div>

          <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
            <h2 class="text-base font-bold text-slate-900 dark:text-white mb-1">Departemen Paling Aktif (3 Bulan Terakhir)</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Unit kerja dengan frekuensi pengajuan pengadaan tertinggi.</p>
            <div id="reports-top-dept-container">Memuat data departemen...</div>
          </div>
        </div>
      </div>
    `;

    try {
      const [sumRes, leadRes, topDeptRes] = await Promise.all([
        this.api('/reports/summary').catch(() => null),
        this.api('/reports/average-lead-time').catch(() => null),
        this.api('/reports/top-departments').catch(() => null)
      ]);

      const sum = sumRes?.result || {};
      const lead = leadRes?.result || {};
      const topDepts = this.extractList(topDeptRes);

      const sumContainer = document.getElementById('reports-summary-cards');
      if (sumContainer) {
        sumContainer.innerHTML = `
          <div class="bg-white dark:bg-slate-800 p-5 rounded-lg border border-slate-200 dark:border-slate-700">
            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Seluruh Permohonan</div>
            <div class="text-2xl font-bold font-mono text-slate-900 dark:text-white mt-1">${sum.total_requests || 0}</div>
            <div class="text-2xs text-slate-400 mt-1">Akumulasi pengadaan unit</div>
          </div>

          <div class="bg-white dark:bg-slate-800 p-5 rounded-lg border border-emerald-200 dark:border-emerald-900/60">
            <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Pengadaan Selesai (Completed)</div>
            <div class="text-2xl font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-1">${sum.completed || 0}</div>
            <div class="text-2xs text-slate-400 mt-1">Barang tiba dan terverifikasi</div>
          </div>

          <div class="bg-white dark:bg-slate-800 p-5 rounded-lg border border-blue-200 dark:border-blue-900/60">
            <div class="text-xs font-semibold text-blue-700 dark:text-blue-400">Dalam Proses PO (In Procurement)</div>
            <div class="text-2xl font-bold font-mono text-blue-700 dark:text-blue-400 mt-1">${sum.in_procurement || 0}</div>
            <div class="text-2xs text-slate-400 mt-1">Menunggu pengiriman rekanan</div>
          </div>

          <div class="bg-white dark:bg-slate-800 p-5 rounded-lg border border-amber-200 dark:border-amber-900/60">
            <div class="text-xs font-semibold text-amber-700 dark:text-amber-400">Menunggu Review Manajer</div>
            <div class="text-2xl font-bold font-mono text-amber-700 dark:text-amber-400 mt-1">${sum.submitted || 0}</div>
            <div class="text-2xs text-slate-400 mt-1">Tahap verifikasi kelayakan</div>
          </div>
        `;
      }

      const leadContainer = document.getElementById('reports-lead-time-container');
      if (leadContainer) {
        leadContainer.innerHTML = `
          <div class="grid grid-cols-3 gap-3 p-4 bg-slate-50 dark:bg-slate-900/50 rounded border border-slate-200 dark:border-slate-700 text-center">
            <div>
              <div class="text-2xs font-semibold text-slate-500 uppercase">Rata-rata Waktu</div>
              <div class="text-xl font-bold font-mono text-slate-900 dark:text-white mt-1">${lead.average_lead_time_days !== undefined ? lead.average_lead_time_days : 0} Hari</div>
            </div>
            <div>
              <div class="text-2xs font-semibold text-emerald-700 dark:text-emerald-400 uppercase">Tercepat</div>
              <div class="text-xl font-bold font-mono text-emerald-700 dark:text-emerald-400 mt-1">${lead.min_lead_time_days !== undefined ? lead.min_lead_time_days : 0} Hari</div>
            </div>
            <div>
              <div class="text-2xs font-semibold text-amber-700 dark:text-amber-400 uppercase">Terlama</div>
              <div class="text-xl font-bold font-mono text-amber-700 dark:text-amber-400 mt-1">${lead.max_lead_time_days !== undefined ? lead.max_lead_time_days : 0} Hari</div>
            </div>
          </div>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-3 leading-relaxed">
            Metrik dihitung otomatis oleh sistem menggunakan fungsi <code>TIMESTAMPDIFF(DAY)</code> antara waktu submit pengajuan hingga pengadaan berstatus rampung.
          </p>
        `;
      }

      const topDeptContainer = document.getElementById('reports-top-dept-container');
      if (topDeptContainer) {
        if (!Array.isArray(topDepts) || topDepts.length === 0) {
          topDeptContainer.innerHTML = `<div class="text-xs text-slate-400 py-4">Belum ada data pengajuan dalam 3 bulan terakhir.</div>`;
        } else {
          topDeptContainer.innerHTML = `
            <div class="space-y-2">
              ${topDepts.map((d, idx) => `
                <div class="flex items-center justify-between p-2.5 rounded bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 text-xs">
                  <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 font-bold text-center leading-5 text-2xs text-slate-700 dark:text-slate-200">${idx + 1}</span>
                    <span class="font-semibold text-slate-900 dark:text-white">${d.department_name || d.name || 'Departemen'}</span>
                  </div>
                  <span class="font-mono font-bold text-emerald-700 dark:text-emerald-400">${d.total_requests || d.count || 0} Pengajuan</span>
                </div>
              `).join('')}
            </div>
          `;
        }
      }
    } catch (err) {
      console.error(err);
    }
  },

  async renderUsersView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <h1 class="text-xl font-bold text-slate-900 dark:text-white">Manajemen Pengguna Sistem (User Accounts)</h1>
          <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Daftar akun operasional procurement beserta perannya masing-masing.</p>
        </div>

        <div id="users-table-container" class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
          <div class="text-center py-12 text-sm text-slate-500">Memuat daftar akun...</div>
        </div>
      </div>
    `;

    try {
      const res = await this.api('/users');
      const users = this.extractList(res);
      const tableContainer = document.getElementById('users-table-container');
      if (!tableContainer) return;

      tableContainer.innerHTML = `
        <div class="table-responsive">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <th class="py-3 px-3 font-semibold">Nama Lengkap</th>
                <th class="py-3 px-3 font-semibold">Email</th>
                <th class="py-3 px-3 font-semibold">Departemen</th>
                <th class="py-3 px-3 font-semibold">Peran (Role)</th>
                <th class="py-3 px-3 font-semibold text-right">Simulasi Akun</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
              ${users.map(u => `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                  <td class="py-3 px-3 font-semibold text-slate-900 dark:text-white">${u.name}</td>
                  <td class="py-3 px-3 font-mono text-slate-600 dark:text-slate-400">${u.email}</td>
                  <td class="py-3 px-3 text-slate-700 dark:text-slate-300">${u.department?.name || '-'}</td>
                  <td class="py-3 px-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-semibold uppercase bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                      ${u.role}
                    </span>
                  </td>
                  <td class="py-3 px-3 text-right">
                    <button onclick="ERP.quickLogin('${u.email}', 'password')" class="px-2 py-1 text-xs font-semibold rounded bg-slate-800 text-white dark:bg-slate-700 hover:bg-slate-900 min-h-[36px]">
                      Masuk Akun Ini
                    </button>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
    } catch (err) {
      const tableContainer = document.getElementById('users-table-container');
      if (tableContainer) {
        tableContainer.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
      }
    }
  },

  async renderDepartmentsView(container) {
    container.innerHTML = `
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-lg border border-slate-200 dark:border-slate-700">
          <h1 class="text-xl font-bold text-slate-900 dark:text-white">Master Departemen & Divisi Perusahaan</h1>
          <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Struktur organisasi unit kerja yang berhak mengajukan pengadaan.</p>
        </div>

        <div id="departments-table-container" class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-4">
          <div class="text-center py-12 text-sm text-slate-500">Memuat data departemen...</div>
        </div>
      </div>
    `;

    try {
      const res = await this.api('/departments');
      const depts = this.extractList(res);
      const tableContainer = document.getElementById('departments-table-container');
      if (!tableContainer) return;

      tableContainer.innerHTML = `
        <div class="table-responsive">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="border-b border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400">
                <th class="py-3 px-3 font-semibold">Kode Divisi</th>
                <th class="py-3 px-3 font-semibold">Nama Departemen</th>
                <th class="py-3 px-3 font-semibold">Deskripsi Tugas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
              ${depts.map(d => `
                <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-750">
                  <td class="py-3 px-3 font-mono font-bold text-slate-900 dark:text-white">${d.code}</td>
                  <td class="py-3 px-3 font-semibold text-slate-800 dark:text-slate-200">${d.name}</td>
                  <td class="py-3 px-3 text-slate-600 dark:text-slate-400">${d.description || '-'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
    } catch (err) {
      const tableContainer = document.getElementById('departments-table-container');
      if (tableContainer) {
        tableContainer.innerHTML = `<div class="p-4 text-xs text-rose-600">${err.message}</div>`;
      }
    }
  },

  closeModal() {
    const modal = document.getElementById('erp-modal');
    if (modal) modal.classList.add('hidden');
  },

  setupEventListeners() {
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeModal();
      }
    });

    const modalBackdrop = document.getElementById('erp-modal-backdrop');
    if (modalBackdrop) {
      modalBackdrop.addEventListener('click', () => this.closeModal());
    }

    const quickSelect = document.getElementById('role-quick-switcher');
    if (quickSelect) {
      quickSelect.addEventListener('change', (e) => {
        const email = e.target.value;
        if (email) {
          this.quickLogin(email, 'password');
        }
      });
    }

    const mobileToggle = document.getElementById('mobile-menu-toggle');
    const sidebar = document.getElementById('sidebar-menu');
    if (mobileToggle && sidebar) {
      mobileToggle.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
      });
    }
  },

  showToast(message, type = 'info') {
    const toast = document.getElementById('erp-toast');
    const toastText = document.getElementById('erp-toast-message');
    if (!toast || !toastText) return;

    toastText.textContent = message;

    const colors = {
      success: 'bg-emerald-800 text-white',
      error: 'bg-rose-800 text-white',
      info: 'bg-slate-800 text-white'
    };

    toast.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-lg shadow-xl text-xs font-semibold flex items-center gap-3 transition-opacity duration-300 ${colors[type] || colors.info}`;
    toast.classList.remove('hidden');

    clearTimeout(this._toastTimeout);
    this._toastTimeout = setTimeout(() => {
      toast.classList.add('hidden');
    }, 4500);
  },

  formatStatusBadge(status) {
    const map = {
      'DRAFT': { text: 'Draft', class: 'badge-draft' },
      'SUBMITTED': { text: 'Menunggu Review', class: 'badge-submitted' },
      'APPROVED': { text: 'Disetujui', class: 'badge-approved' },
      'REJECTED': { text: 'Ditolak', class: 'badge-rejected' },
      'IN_PROCUREMENT': { text: 'Dalam Pengadaan (PO)', class: 'badge-procurement' },
      'COMPLETED': { text: 'Selesai', class: 'badge-completed' }
    };

    const item = map[status] || { text: status, class: 'badge-draft' };
    return `<span class="badge-status ${item.class}">${item.text}</span>`;
  },

  formatRupiah(value) {
    if (!value && value !== 0) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0
    }).format(value);
  },

  formatDate(dateString) {
    if (!dateString) return '-';
    try {
      const d = new Date(dateString);
      return d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
    } catch {
      return dateString;
    }
  }
};

window.addEventListener('DOMContentLoaded', () => {
  ERP.init();
});
