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
