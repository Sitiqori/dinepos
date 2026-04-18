/* =============================================
   DINE POS — app.js
   Global interactions & utilities
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

  // ── Sidebar toggle (mobile) ──────────────────
  const sidebar       = document.getElementById('sidebar');
  const overlay       = document.getElementById('sidebarOverlay');
  const toggleBtn     = document.getElementById('sidebarToggle');

  function openSidebar()  { sidebar?.classList.add('open'); overlay?.classList.add('show'); }
  function closeSidebar() { sidebar?.classList.remove('open'); overlay?.classList.remove('show'); }

  toggleBtn?.addEventListener('click', () => {
    sidebar?.classList.contains('open') ? closeSidebar() : openSidebar();
  });
  overlay?.addEventListener('click', closeSidebar);

  // ── Submenu toggle ───────────────────────────
  document.querySelectorAll('.nav-item.has-sub').forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      item.classList.toggle('open');
    });
  });

  // ── Active nav highlight ─────────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-item[href]').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });

  // ── Auto-close alerts ────────────────────────
  document.querySelectorAll('.alert[data-auto-close]').forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-8px)';
      alert.style.transition = 'all .3s ease';
      setTimeout(() => alert.remove(), 300);
    }, 4000);
  });

  // ── Tooltip (title attr) ─────────────────────
  document.querySelectorAll('[data-tooltip]').forEach(el => {
    el.setAttribute('title', el.dataset.tooltip);
  });

  // ── Confirm delete ───────────────────────────
  document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (!confirm('Yakin ingin menghapus data ini?')) {
        e.preventDefault();
      }
    });
  });

  // ── Format currency inputs ───────────────────
  document.querySelectorAll('.currency-input').forEach(input => {
    input.addEventListener('input', function () {
      let val = this.value.replace(/\D/g, '');
      this.value = val ? parseInt(val).toLocaleString('id-ID') : '';
    });
  });

  // ── Table row click ──────────────────────────
  document.querySelectorAll('tr[data-href]').forEach(row => {
    row.style.cursor = 'pointer';
    row.addEventListener('click', () => {
      window.location.href = row.dataset.href;
    });
  });

  console.log('%cDINE POS 🍽️', 'color:#0f1e3c;font-size:18px;font-weight:bold;');
  console.log('%cAdmin Dashboard Ready', 'color:#1d4ed8;');
});

// ── Helpers ────────────────────────────────────
const DinePOS = {
  formatRupiah(val) {
    return 'Rp ' + parseInt(val).toLocaleString('id-ID');
  },
  formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
  },
  toast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `toast toast-${type}`;
    t.textContent = msg;
    Object.assign(t.style, {
      position: 'fixed', bottom: '24px', right: '24px',
      background: type === 'success' ? '#10b981' : '#ef4444',
      color: '#fff', padding: '12px 20px', borderRadius: '10px',
      fontWeight: '600', fontSize: '.875rem', zIndex: 9999,
      boxShadow: '0 4px 16px rgba(0,0,0,.15)',
      animation: 'slideUp .3s ease'
    });
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; t.style.transition = 'all .3s'; setTimeout(() => t.remove(), 300); }, 3000);
  }
};

window.DinePOS = DinePOS;

/* =============================================
   DINE POS — Push Notification System
   Pop-up toast di pojok kanan bawah, semua halaman
   ============================================= */
(function () {
  // Hanya aktif kalau user sudah login (ada meta csrf)
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  if (!csrfMeta) return;
  const CSRF = csrfMeta.content;

  // ── Container ─────────────────────────────────
  const container = document.createElement('div');
  container.id = 'dinepos-notif-container';
  Object.assign(container.style, {
    position:      'fixed',
    bottom:        '24px',
    right:         '24px',
    zIndex:        '99999',
    display:       'flex',
    flexDirection: 'column-reverse',
    gap:           '10px',
    maxWidth:      '340px',
    width:         '100%',
    pointerEvents: 'none',
  });
  document.body.appendChild(container);

  // ── CSS ───────────────────────────────────────
  const style = document.createElement('style');
  style.textContent = `
    .dp-toast {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 14px 16px;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 8px 32px rgba(0,0,0,.16), 0 2px 8px rgba(0,0,0,.08);
      border-left: 4px solid #1d4ed8;
      pointer-events: all;
      cursor: pointer;
      animation: dpSlideIn .35s cubic-bezier(.34,1.56,.64,1);
      transition: opacity .3s, transform .3s;
      max-width: 340px;
      width: 100%;
    }
    .dp-toast.dp-success { border-left-color: #10b981; }
    .dp-toast.dp-warning { border-left-color: #f59e0b; }
    .dp-toast.dp-error   { border-left-color: #ef4444; }
    .dp-toast.dp-info    { border-left-color: #1d4ed8; }
    .dp-toast.dp-out     { opacity: 0; transform: translateX(20px); }
    .dp-toast-icon {
      width: 32px; height: 32px; border-radius: 8px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; flex-shrink: 0;
    }
    .dp-toast.dp-success .dp-toast-icon { background: #f0fdf4; color: #10b981; }
    .dp-toast.dp-warning .dp-toast-icon { background: #fff7ed; color: #f59e0b; }
    .dp-toast.dp-error   .dp-toast-icon { background: #fff1f1; color: #ef4444; }
    .dp-toast.dp-info    .dp-toast-icon { background: #eff6ff; color: #1d4ed8; }
    .dp-toast-body { flex: 1; min-width: 0; }
    .dp-toast-title { font-weight: 700; font-size: .82rem; color: #0f1e3c; margin-bottom: 2px; }
    .dp-toast-msg   { font-size: .75rem; color: #64748b; line-height: 1.4; }
    .dp-toast-close {
      flex-shrink: 0; background: none; border: none;
      color: #94a3b8; cursor: pointer; font-size: 1rem;
      padding: 0; line-height: 1; pointer-events: all;
    }
    .dp-toast-close:hover { color: #0f1e3c; }
    .dp-toast-progress {
      position: absolute; bottom: 0; left: 0;
      height: 3px; border-radius: 0 0 12px 12px;
      background: currentColor; opacity: .25;
      animation: dpProgress linear forwards;
    }
    @keyframes dpSlideIn {
      from { opacity: 0; transform: translateX(40px) scale(.92); }
      to   { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes dpProgress {
      from { width: 100%; }
      to   { width: 0%; }
    }
  `;
  document.head.appendChild(style);

  // ── Show toast ────────────────────────────────
  const shown = new Set(); // deduplicate by key

  function showToast({ title, msg, type = 'info', icon = 'ri-notification-3-line', duration = 5000, key = null, url = null }) {
    if (key && shown.has(key)) return;
    if (key) shown.add(key);

    const toast = document.createElement('div');
    toast.className = `dp-toast dp-${type}`;
    toast.style.position = 'relative';
    toast.style.overflow  = 'hidden';
    toast.innerHTML = `
      <div class="dp-toast-icon"><i class="${icon}"></i></div>
      <div class="dp-toast-body">
        <div class="dp-toast-title">${title}</div>
        <div class="dp-toast-msg">${msg}</div>
      </div>
      <button class="dp-toast-close" title="Tutup">✕</button>
      <div class="dp-toast-progress" style="animation-duration:${duration}ms;"></div>
    `;

    if (url) toast.addEventListener('click', (e) => { if (e.target.closest('.dp-toast-close')) return; window.location.href = url; });

    toast.querySelector('.dp-toast-close').addEventListener('click', () => dismiss(toast));
    container.appendChild(toast);

    const timer = setTimeout(() => dismiss(toast), duration);
    toast._timer = timer;
  }

  function dismiss(toast) {
    clearTimeout(toast._timer);
    toast.classList.add('dp-out');
    setTimeout(() => toast.remove(), 320);
  }

  // ── Poll endpoint ─────────────────────────────
  let lastCheck = Math.floor(Date.now() / 1000);

  async function pollEvents() {
    try {
      const res = await fetch(`/notifikasi/events?since=${lastCheck}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
      });
      if (!res.ok) return;
      const data = await res.json();
      lastCheck = data.now ?? Math.floor(Date.now() / 1000);

      // Pesanan baru
      (data.pesanan_baru ?? []).forEach(o => {
        showToast({
          key:   `pesanan-${o.id}`,
          type:  'info',
          icon:  'ri-shopping-bag-3-line',
          title: '🛎️ Pesanan Baru Masuk!',
          msg:   `#${o.order_code} · Rp ${parseInt(o.total).toLocaleString('id-ID')}`,
          url:   `/pesanan?search=${o.order_code}&status=all`,
        });
      });

      // Pesanan status berubah
      (data.status_update ?? []).forEach(o => {
        const label = { processing: 'Diproses', completed: 'Selesai ✅', cancelled: 'Dibatalkan ❌' }[o.status] ?? o.status;
        const type  = { completed: 'success', cancelled: 'error', processing: 'info' }[o.status] ?? 'info';
        showToast({
          key:   `status-${o.id}-${o.status}`,
          type,
          icon:  'ri-receipt-line',
          title: `Pesanan ${label}`,
          msg:   `#${o.order_code}`,
          url:   `/pesanan?search=${o.order_code}&status=all`,
        });
      });

      // Bayar berhasil
      (data.bayar_berhasil ?? []).forEach(t => {
        showToast({
          key:   `bayar-${t.id}`,
          type:  'success',
          icon:  'ri-checkbox-circle-line',
          title: 'Pembayaran Berhasil 💰',
          msg:   `${t.invoice_code} · Rp ${parseInt(t.amount).toLocaleString('id-ID')} · ${t.payment_method?.toUpperCase()}`,
          duration: 6000,
        });
      });

      // Stok menipis (hanya sekali per produk per sesi)
      (data.stok_menipis ?? []).forEach(p => {
        showToast({
          key:   `stok-${p.id}`,
          type:  'warning',
          icon:  'ri-alert-line',
          title: '⚠️ Stok Menipis',
          msg:   `${p.name} — tersisa ${p.stock}`,
          url:   '/barang',
          duration: 7000,
        });
      });

      // Kadaluarsa mendekat
      (data.kadaluarsa ?? []).forEach(p => {
        showToast({
          key:   `exp-${p.id}`,
          type:  'warning',
          icon:  'ri-calendar-close-line',
          title: '📅 Segera Kadaluarsa',
          msg:   `${p.name} — ${p.sisa_hari} hari lagi`,
          url:   '/notifikasi',
          duration: 7000,
        });
      });

      // Sudah kadaluarsa
      (data.sudah_kadaluarsa ?? []).forEach(p => {
        showToast({
          key:   `expired-${p.id}`,
          type:  'error',
          icon:  'ri-error-warning-line',
          title: '🚨 Produk Kadaluarsa!',
          msg:   `${p.name} sudah melewati tanggal kadaluarsa`,
          url:   '/notifikasi',
          duration: 8000,
        });
      });

    } catch (_) { /* silent fail */ }
  }

  // Mulai polling setelah 3 detik (beri waktu halaman load)
  setTimeout(() => {
    pollEvents();
    setInterval(pollEvents, 15000); // tiap 15 detik
  }, 3000);

  // Expose supaya kasir bisa trigger manual setelah bayar
  window.DinePOS.showToast = showToast;
})();