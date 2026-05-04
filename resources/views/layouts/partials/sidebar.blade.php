<aside class="sidebar" id="sidebar">

  {{-- Logo --}}
  <div class="sidebar-logo">
    <img src="{{ asset('images/Logo(mini_dark).png') }}" alt="DINE POS Logo" />
    <span class="brand-name">DINE POS</span>
  </div>

  {{-- Navigation --}}
  <nav class="sidebar-nav">

    {{-- ── ADMIN MENU ── --}}
    @if(auth()->user()->role === 'admin')

      <div class="nav-section-label">Main</div>

      <a href="{{ route('dashboard') }}"
         class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="ri-home-5-line"></i>
        <span>Admin Dashboard</span>
      </a>

      @php $totalNotifCount = $sidebarNotifCount ?? 0 @endphp

      <a href="{{ route('kasir.index') }}"
         class="nav-item {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
        <i class="ri-shopping-cart-2-line"></i>
        <span>Kasir</span>
      </a>

      <div class="nav-section-label">Inventori</div>

      {{-- Barang & Stok (with submenu) --}}
      @php $lowStock = \App\Models\Product::whereRaw('stock < min_stock')->count() @endphp
      <div class="nav-item has-sub {{ request()->routeIs('barang.*') || request()->routeIs('kategori.*') ? 'open active' : '' }}">
        <i class="ri-stack-line"></i>
        <span>Barang &amp; Stok</span>
        @if($lowStock > 0)
          <span class="nav-badge">{{ $lowStock }}</span>
        @endif
        <i class="ri-arrow-right-s-line chevron"></i>
      </div>
      <div class="nav-submenu">
        <a href="{{ route('barang.index') }}"
          class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
          <span>Daftar Barang</span>
          @if($lowStock > 0)
            <span class="nav-badge">{{ $lowStock }}</span>
          @endif
        </a>
        <a href="{{ route('kategori.index') }}"
           class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
          <span>Kategori</span>
        </a>
      </div>

      <div class="nav-section-label">Transaksi</div>

      <a href="{{ route('pesanan.index') }}"
         class="nav-item {{ request()->routeIs('pesanan.*') ? 'active' : '' }}">
        <i class="ri-file-list-3-line"></i>
        <span>Pesanan</span>
        @php $pendingOrders = \App\Models\Order::where('status','pending')->count() @endphp
        @if($pendingOrders > 0)
          <span class="nav-badge">{{ $pendingOrders }}</span>
        @endif
      </a>

      <a href="{{ route('transaksi.index') }}"
         class="nav-item {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
        <i class="ri-exchange-dollar-line"></i>
        <span>Riwayat Transaksi</span>
      </a>

      <div class="nav-section-label">Laporan</div>

      <a href="{{ route('laporan.index') }}"
         class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
        <i class="ri-bar-chart-2-line"></i>
        <span>Laporan Penjualan</span>
      </a>

      <div class="nav-section-label">Pengaturan</div>

      <a href="{{ route('admin.index') }}"
         class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
        <i class="ri-user-settings-line"></i>
        <span>Manajemen Admin</span>
      </a>

      <a href="{{ route('pengaturan.index') }}"
         class="nav-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
        <i class="ri-settings-3-line"></i>
        <span>Pengaturan</span>
      </a>

    @else
    {{-- ── KASIR MENU ── --}}

      <div class="nav-section-label">Menu Kasir</div>

      <a href="{{ route('kasir.index') }}"
         class="nav-item {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
        <i class="ri-shopping-cart-2-line"></i>
        <span>Kasir</span>
      </a>

      <a href="{{ route('pesanan.index') }}"
         class="nav-item {{ request()->routeIs('pesanan.*') ? 'active' : '' }}">
        <i class="ri-file-list-3-line"></i>
        <span>Pesanan</span>
      </a>

      <a href="{{ route('transaksi.index') }}"
         class="nav-item {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
        <i class="ri-exchange-dollar-line"></i>
        <span>Riwayat Transaksi</span>
      </a>

    @endif

  </nav>

  {{-- User info + logout --}}
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <div class="user-info">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-role">{{ ucfirst(auth()->user()->role) }}</div>
      </div>
      <a href="#"
         onclick="confirmLogout(event)"
         class="logout-btn" data-tooltip="Keluar">
        <i class="ri-logout-box-r-line"></i>
      </a>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
      @csrf
    </form>
  </div>

  {{-- Logout Confirm Modal --}}
  <div id="logoutModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:28px 32px;max-width:340px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.18);">
      <div style="font-size:2rem;margin-bottom:8px;">👋</div>
      <div style="font-size:1.1rem;font-weight:700;color:#1a1a2e;margin-bottom:6px;">Yakin ingin keluar?</div>
      <div style="font-size:.875rem;color:#6b7280;margin-bottom:22px;">Sesi kamu akan diakhiri.</div>
      <div style="display:flex;gap:10px;justify-content:center;">
        <button onclick="document.getElementById('logoutModal').style.display='none'"
          style="padding:9px 22px;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;font-size:.875rem;font-weight:600;cursor:pointer;color:#374151;">
          Batal
        </button>
        <button onclick="document.getElementById('logout-form').submit()"
          style="padding:9px 22px;border-radius:8px;border:none;background:#e53935;color:#fff;font-size:.875rem;font-weight:600;cursor:pointer;">
          Ya, Keluar
        </button>
      </div>
    </div>
  </div>

  <script>
  function confirmLogout(e) {
    e.preventDefault();
    const m = document.getElementById('logoutModal');
    m.style.display = 'flex';
    m.addEventListener('click', function(ev){ if(ev.target===m) m.style.display='none'; });
  }
  </script>

</aside>