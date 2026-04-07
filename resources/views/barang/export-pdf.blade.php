<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Laporan Stok Barang — DINE POS</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; padding: 24px; }
    .header { text-align: center; margin-bottom: 20px; }
    .header h1 { font-size: 16px; font-weight: 700; color: #0f1e3c; }
    .header p  { font-size: 11px; color: #64748b; margin-top: 4px; }
    .badge { display: inline-block; background: #0f1e3c; color: #fff; padding: 2px 10px; border-radius: 99px; font-size: 10px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    thead th { background: #0f1e3c; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
    .low  { color: #ef4444; font-weight: 700; }
    .foot { margin-top: 16px; font-size: 10px; color: #64748b; text-align: center; }
    @media print {
      body { padding: 10px; }
      button { display: none; }
    }
  </style>
</head>
<body>
  <div style="text-align:right;margin-bottom:12px;">
    <button onclick="window.print()" style="padding:8px 16px;background:#0f1e3c;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:12px;">
      🖨️ Cetak PDF
    </button>
  </div>

  <div class="header">
    <h1>DINE POS — Laporan Stok Barang</h1>
    <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</p>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Kode</th>
        <th>Nama Barang</th>
        <th>Kategori</th>
        <th>Satuan</th>
        <th>HPP</th>
        <th>Harga Jual</th>
        <th>Stok</th>
        <th>Min. Stok</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $i => $p)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td style="font-family:monospace;font-weight:700;">{{ $p->sku ?? '-' }}</td>
        <td style="font-weight:600;">{{ $p->name }}</td>
        <td>{{ $p->category?->name ?? '-' }}</td>
        <td>{{ $p->unit ?? 'Pcs' }}</td>
        <td>Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
        <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
        <td class="{{ $p->isLowStock() ? 'low' : '' }}">{{ $p->stock }}</td>
        <td>{{ $p->min_stock ?? 0 }}</td>
        <td>
          @if($p->is_active)
            <span class="badge">Aktif</span>
          @else
            <span style="color:#64748b;">Nonaktif</span>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="foot">
    Total: {{ $products->count() }} barang — DINE POS &copy; {{ date('Y') }}
  </div>
</body>
</html>
