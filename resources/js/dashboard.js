/* =============================================
   DINE POS — dashboard.js
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {
  const ctx = document.getElementById('salesChart');
  if (!ctx || !window.DASHBOARD_DATA) return;

  const D = window.DASHBOARD_DATA;

  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: D.labels,
      datasets: [{
        label: 'Total Penjualan',
        data: D.penjualan,
        backgroundColor: '#0f1e3c',
        borderRadius: 6,
        borderSkipped: false,
        barPercentage: 0.55,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f1e3c',
          titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '600' },
          bodyFont:  { family: 'Plus Jakarta Sans', size: 11 },
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: function (ctx) {
              const ds = document.querySelector('.chart-tab.active')?.dataset?.ds || 'penjualan';
              if (ds === 'transaksi') return ' ' + ctx.raw + ' transaksi';
              return ' Rp ' + parseInt(ctx.raw).toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#64748b', font: { size: 11 } },
          border: { display: false }
        },
        y: {
          grid: { color: '#f1f5f9' },
          ticks: {
            color: '#64748b',
            font: { size: 11 },
            callback: function (val) {
              const ds = document.querySelector('.chart-tab.active')?.dataset?.ds || 'penjualan';
              if (ds === 'transaksi') return val;
              if (val >= 1000000) return (val / 1000000).toFixed(1) + 'Jt';
              if (val >= 1000)    return (val / 1000).toFixed(0) + 'Rb';
              return val;
            }
          },
          border: { display: false }
        }
      }
    }
  });

  // Expose globally so inline script can update it
  window._chart = chart;

  // Animate stat numbers
  document.querySelectorAll('.stat-number[data-val]').forEach(el => {
    const target = parseInt(el.dataset.val) || 0;
    let current = 0;
    const step  = Math.max(1, target / 40);
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = Math.floor(current).toLocaleString('id-ID');
      if (current >= target) clearInterval(timer);
    }, 20);
  });
});
