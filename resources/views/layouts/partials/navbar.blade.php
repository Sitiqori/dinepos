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
    <a href="{{ route('notifikasi.index') }}" class="notif-btn {{ request()->routeIs('notifikasi.*') ? 'notif-btn-active' : '' }}" id="notifBtn" title="Notifikasi">
      <i class="ri-notification-3-line"></i>
      <span class="notif-dot" id="notifDot" style="display:none;"></span>
      <span class="notif-count-badge" id="notifCount" style="display:none;"></span>
    </a>

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

@push('scripts')
<script>
(function pollNotif() {
  fetch('{{ route("notifikasi.count") }}', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
    .then(r => r.json())
    .then(data => {
      const dot   = document.getElementById('notifDot');
      const badge = document.getElementById('notifCount');
      if (data.total > 0) {
        dot.style.display   = 'block';
        badge.style.display = 'flex';
        badge.textContent   = data.total > 99 ? '99+' : data.total;
      } else {
        dot.style.display   = 'none';
        badge.style.display = 'none';
      }
    })
    .catch(() => {});
  setTimeout(pollNotif, 60000); // refresh every 60s
})();
</script>
@endpush