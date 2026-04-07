<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Check which optional columns exist in the DB so we stay
     * compatible whether the user has run the latest migration or not.
     */
    private function hasColumn(string $col): bool
    {
        static $cols = null;
        if ($cols === null) {
            $cols = Schema::getColumnListing('products');
        }
        return in_array($col, $cols);
    }

    /* ─── INDEX ──────────────────────────────── */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
                if ($this->hasColumn('sku')) {
                    $q->orWhere('sku', 'like', '%'.$request->search.'%');
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        // Low stock: use min_stock column if it exists, else fallback to <= 5
        $lowStockCount = $this->hasColumn('min_stock')
            ? Product::whereColumn('stock', '<=', 'min_stock')->count()
            : Product::where('stock', '<=', 5)->count();

        return view('barang.index', compact('products', 'categories', 'lowStockCount'));
    }

    /* ─── STORE (AJAX JSON) ───────────────────── */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'integer', 'min:0'],
            'cost_price'  => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'is_active'   => ['nullable'],
        ];

        // Only validate new fields if the columns actually exist
        if ($this->hasColumn('sku')) {
            $rules['sku'] = ['nullable', 'string', 'max:50', 'unique:products,sku'];
        }
        if ($this->hasColumn('barcode')) {
            $rules['barcode'] = ['nullable', 'string', 'max:100'];
        }
        if ($this->hasColumn('unit')) {
            $rules['unit'] = ['nullable', 'string', 'max:30'];
        }
        if ($this->hasColumn('min_stock')) {
            $rules['min_stock'] = ['nullable', 'integer', 'min:0'];
        }

        $data = $request->validate($rules);

        $data['slug']      = Str::slug($data['name']).'-'.Str::random(4);
        $data['is_active'] = $request->boolean('is_active', true);

        // Default unit if column exists but not provided
        if ($this->hasColumn('unit') && empty($data['unit'])) {
            $data['unit'] = 'Pcs';
        }
        if ($this->hasColumn('min_stock') && !isset($data['min_stock'])) {
            $data['min_stock'] = 0;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Remove keys for columns that don't exist
        foreach (['sku','barcode','unit','min_stock'] as $col) {
            if (!$this->hasColumn($col)) unset($data[$col]);
        }

        $product = Product::create($data);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan.',
            'product' => $product,
        ]);
    }

    /* ─── SHOW (AJAX JSON) ────────────────────── */
    public function show(Product $barang): JsonResponse
    {
        $barang->load('category');
        return response()->json($barang);
    }

    /* ─── UPDATE (AJAX JSON) ──────────────────── */
    public function update(Request $request, Product $barang): JsonResponse
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'integer', 'min:0'],
            'cost_price'  => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'],
            'is_active'   => ['nullable'],
        ];

        if ($this->hasColumn('sku')) {
            $rules['sku'] = ['nullable', 'string', 'max:50', 'unique:products,sku,'.$barang->id];
        }
        if ($this->hasColumn('barcode')) {
            $rules['barcode'] = ['nullable', 'string', 'max:100'];
        }
        if ($this->hasColumn('unit')) {
            $rules['unit'] = ['nullable', 'string', 'max:30'];
        }
        if ($this->hasColumn('min_stock')) {
            $rules['min_stock'] = ['nullable', 'integer', 'min:0'];
        }

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($barang->image) Storage::disk('public')->delete($barang->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Remove keys for columns that don't exist
        foreach (['sku','barcode','unit','min_stock'] as $col) {
            if (!$this->hasColumn($col)) unset($data[$col]);
        }

        $barang->update($data);
        $barang->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diperbarui.',
            'product' => $barang,
        ]);
    }

    /* ─── DESTROY (AJAX JSON) ─────────────────── */
    public function destroy(Product $barang): JsonResponse
    {
        if ($barang->orderItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak bisa dihapus karena sudah pernah ada di transaksi.',
            ], 422);
        }

        if ($barang->image) Storage::disk('public')->delete($barang->image);
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus.',
        ]);
    }

    /* ─── EXPORT PDF ──────────────────────────── */
    public function exportPdf()
    {
        $products = Product::with('category')->orderBy(
            $this->hasColumn('sku') ? 'sku' : 'name'
        )->get();

        return view('barang.export-pdf', compact('products'));
    }

    /* ─── EXPORT CSV ──────────────────────────── */
    public function exportCsv()
    {
        $products = Product::with('category')->orderBy(
            $this->hasColumn('sku') ? 'sku' : 'name'
        )->get();

        $hasSku      = $this->hasColumn('sku');
        $hasUnit     = $this->hasColumn('unit');
        $hasMinStock = $this->hasColumn('min_stock');

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="barang-'.now()->format('Ymd').'.csv"',
        ];

        $callback = function () use ($products, $hasSku, $hasUnit, $hasMinStock) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

            $header = ['#', 'Nama Barang', 'Kategori', 'HPP', 'Harga Jual', 'Stok', 'Status'];
            if ($hasSku)      array_splice($header, 1, 0, ['Kode Barang']);
            if ($hasUnit)     array_splice($header, $hasSku ? 4 : 3, 0, ['Satuan']);
            if ($hasMinStock) array_splice($header, count($header) - 1, 0, ['Min. Stok']);
            fputcsv($f, $header);

            foreach ($products as $i => $p) {
                $row = [$i + 1, $p->name, $p->category?->name ?? '-', $p->cost_price, $p->price, $p->stock, $p->is_active ? 'Aktif' : 'Nonaktif'];
                if ($hasSku)      array_splice($row, 1, 0, [$p->sku ?? '-']);
                if ($hasUnit)     array_splice($row, $hasSku ? 4 : 3, 0, [$p->unit ?? 'Pcs']);
                if ($hasMinStock) array_splice($row, count($row) - 1, 0, [$p->min_stock ?? 0]);
                fputcsv($f, $row);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }
}
