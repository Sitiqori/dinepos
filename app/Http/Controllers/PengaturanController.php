<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        // Safe: settings table might not exist yet
        $settings = [];
        if (Schema::hasTable('settings')) {
            $settings = Setting::allKeyed();
        }

        return view('pengaturan.index', compact('settings'));
    }

    /* ─── Simpan Profil Toko ─────────────────── */
    public function updateProfil(Request $request)
    {
        $data = $request->validate([
            'store_name'     => ['required', 'string', 'max:255'],
            'store_address'  => ['required', 'string'],
            'store_phone'    => ['required', 'string', 'max:30'],
            'store_whatsapp' => ['required', 'string', 'max:30'],
            'store_npwp'     => ['nullable', 'string', 'max:50'],
            'store_logo'     => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo
            $oldLogo = Setting::get('store_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $data['store_logo'] = $request->file('store_logo')->store('settings', 'public');
        } else {
            unset($data['store_logo']);
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success_profil', 'Profil toko berhasil disimpan.');
    }

    /* ─── Simpan Struk & Pajak ───────────────── */
    public function updatePajak(Request $request)
    {
        $request->validate([
            'max_discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'rounding'     => ['required', 'in:none,100,500,1000'],
            'ppn_enabled'  => ['nullable'],
        ]);

        Setting::set('ppn_enabled',  $request->boolean('ppn_enabled') ? '1' : '0');
        Setting::set('max_discount', $request->input('max_discount', '0'));
        Setting::set('rounding',     $request->input('rounding', 'none'));

        return back()->with('success_pajak', 'Pengaturan pajak berhasil disimpan.');
    }

    /* ─── Simpan Format Nomor ────────────────── */
    public function updateFormat(Request $request)
    {
        $request->validate([
            'invoice_format' => ['nullable', 'string', 'max:50'],
            'invoice_reset'  => ['required', 'in:daily,monthly,yearly,never'],
        ]);

        Setting::set('invoice_format', $request->input('invoice_format', 'DD-MM-YYYY'));
        Setting::set('invoice_reset',  $request->input('invoice_reset', 'monthly'));

        return back()->with('success_format', 'Format nomor berhasil disimpan.');
    }

    /* ─── Simpan Printer ─────────────────────── */
    public function updatePrinter(Request $request)
    {
        $request->validate([
            'printer_type'   => ['required', 'in:thermal_58,thermal_80,a4'],
            'printer_copies' => ['required', 'integer', 'min:1', 'max:5'],
            'auto_print'     => ['nullable'],
        ]);

        Setting::set('printer_type',   $request->input('printer_type', 'thermal_80'));
        Setting::set('printer_copies', $request->input('printer_copies', '1'));
        Setting::set('auto_print',     $request->boolean('auto_print') ? '1' : '0');

        return back()->with('success_printer', 'Pengaturan printer berhasil disimpan.');
    }
}
