/* =============================================
   DINE POS — dashboard.js
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

  // ── Chart setup ─────────────────────────────
  const ctx = document.getElementById('salesChart');
  if (!ctx) return;

  // Data passed from blade (or mock)
  const chartData = window.DASHBOARD_DATA || {
    labels: ['11 Nov', '12 Nov', '13 Nov', '14 Nov', '15 Nov'],
    transaksi: [8, 14, 11, 17, 12],
    penjualan: [1200000, 1650000, 1450000, 1950000, 1350000],
    laba: [560000, 800000, 690000, 980000, 620000],
  };

  let activeDataset = 'penjualan';

  const chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: chartData.labels,
      datasets: [{
        label: 'Total Penjualan',
        data: chartData.penjualan,
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
          bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
          padding: 10,
          cornerRadius: 8,
          callbacks: {
            label: function (context) {
              if (activeDataset === 'transaksi') {
                return ' ' + context.raw + ' transaksi';
              }
              return ' Rp ' + parseInt(context.raw).toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#64748b', font: { size: 11, family: 'Plus Jakarta Sans' } },
          border: { display: false }
        },
        y: {
          grid: { color: '#f1f5f9', drawBorder: false },
          ticks: {
            color: '#64748b',
            font: { size: 11, family: 'Plus Jakarta Sans' },
            callback: function (val) {
              if (activeDataset === 'transaksi') return val;
              if (val >= 1000000) return (val / 1000000).toFixed(1) + 'Jt';
              if (val >= 1000) return (val / 1000).toFixed(0) + 'Rb';
              return val;
            }
          },
          border: { display: false }
        }
      }
    }
  });

  // ── Tab switching ────────────────────────────
  document.querySelectorAll('.chart-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      activeDataset = this.dataset.ds;

      let newData, label, color;
      switch (activeDataset) {
        case 'transaksi':
          newData = chartData.transaksi; label = 'Total Transaksi'; color = '#1d4ed8'; break;
        case 'laba':
          newData = chartData.laba; label = 'Laba Kotor'; color = '#7c3aed'; break;
        default:
          newData = chartData.penjualan; label = 'Total Penjualan'; color = '#0f1e3c'; break;
      }
      chartInstance.data.datasets[0].data = newData;
      chartInstance.data.datasets[0].label = label;
      chartInstance.data.datasets[0].backgroundColor = color;

      const chartTitle = document.getElementById('chartTitle');
      if (chartTitle) chartTitle.textContent = label;

      chartInstance.update('active');
    });
  });

  // ── Month selector ───────────────────────────
  document.getElementById('monthSelect')?.addEventListener('change', function () {
    // In real app: fetch new data via AJAX
    // For demo: randomize
    const randomize = arr => arr.map(v => Math.floor(v * (0.7 + Math.random() * 0.6)));
    chartInstance.data.datasets[0].data = randomize(
      activeDataset === 'transaksi' ? chartData.transaksi :
      activeDataset === 'laba' ? chartData.laba : chartData.penjualan
    );
    chartInstance.update('active');
  });

  // ── Download report ──────────────────────────
  document.getElementById('downloadReport')?.addEventListener('click', function () {
    DinePOS.toast('Laporan sedang diunduh...', 'success');
    // Real impl: window.location.href = '/laporan/download?month=...'
  });

  // ── Animate stat numbers ─────────────────────
  document.querySelectorAll('.stat-number[data-val]').forEach(el => {
    const target = parseInt(el.dataset.val);
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';
    let current = 0;
    const step = target / 40;
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = prefix + Math.floor(current).toLocaleString('id-ID') + suffix;
      if (current >= target) clearInterval(timer);
    }, 20);
  });

});
