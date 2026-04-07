<header class="topbar">
  <div class="topbar-left">
    {{-- Mobile toggle --}}
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
      <i class="ri-menu-line"></i>
    </button>

    {{-- Page title (injected per page) --}}
    <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
  </div>

  <div class="topbar-right">
    {{-- Search --}}
    <div class="search-box">
      <i class="ri-search-line"></i>
      <input type="text" placeholder="Cari..." id="globalSearch" autocomplete="off" />
    </div>

    {{-- Notifications --}}
    <div class="notif-btn" id="notifBtn" title="Notifikasi">
      <i class="ri-notification-3-line"></i>
      <span class="notif-dot"></span>
    </div>

    {{-- User chip --}}
    <div class="topbar-user" id="topbarUser">
      <div class="ta-avatar">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <span class="ta-name">{{ auth()->user()->name }}</span>
      <i class="ri-arrow-down-s-line" style="color:var(--text-muted);font-size:.85rem;"></i>
    </div>
  </div>
</header>
