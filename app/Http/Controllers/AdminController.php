<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /* ─── INDEX ──────────────────────────────── */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();

        $hasIsActive = Schema::hasColumn('users', 'is_active');

        $stats = [
            'owner'  => $users->where('role', 'admin')->count(),
            'kasir'  => $users->where('role', 'kasir')->count(),
            'total'  => $users->count(),
            'active' => $hasIsActive
                ? $users->filter(fn($u) => $u->is_active)->count()
                : $users->count(),
        ];

        return view('admin.index', compact('users', 'stats'));
    }

    /* ─── SHOW (AJAX JSON) ────────────────────── */
    public function show(User $admin): JsonResponse
    {
        return response()->json([
            'id'            => $admin->id,
            'name'          => $admin->name,
            'email'         => $admin->email,
            'role'          => $admin->role,
            'phone'         => $admin->phone ?? null,
            'address'       => $admin->address ?? null,
            'branch'        => $admin->branch ?? null,
            'join_date'     => $admin->join_date?->format('Y-m-d') ?? null,
            'is_active'     => $admin->is_active,
            'photo_url'     => $admin->photo_url,
            'initials'      => $admin->initials,
        ]);
    }

    /* ─── STORE (AJAX JSON) ───────────────────── */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', 'in:admin,kasir'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
            'branch'   => ['nullable', 'string', 'max:100'],
            'join_date'=> ['nullable', 'date'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ];

        $data = $request->validate($rules);

        $userData = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'is_active'=> true,
        ];

        if (Schema::hasColumn('users', 'phone'))     $userData['phone']     = $data['phone'] ?? null;
        if (Schema::hasColumn('users', 'address'))   $userData['address']   = $data['address'] ?? null;
        if (Schema::hasColumn('users', 'branch'))    $userData['branch']    = $data['branch'] ?? null;
        if (Schema::hasColumn('users', 'join_date')) $userData['join_date'] = $data['join_date'] ?? null;

        if ($request->hasFile('photo') && Schema::hasColumn('users', 'profile_photo')) {
            $userData['profile_photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil ditambahkan.',
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
                'phone'    => $user->phone,
                'branch'   => $user->branch,
                'is_active'=> $user->is_active,
                'photo_url'=> $user->photo_url,
                'initials' => $user->initials,
            ],
        ]);
    }

    /* ─── UPDATE (AJAX JSON) ──────────────────── */
    public function update(Request $request, User $admin): JsonResponse
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,'.$admin->id],
            'role'     => ['required', 'in:admin,kasir'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
            'branch'   => ['nullable', 'string', 'max:100'],
            'join_date'=> ['nullable', 'date'],
            'photo'    => ['nullable', 'image', 'max:2048'],
        ];

        $data = $request->validate($rules);

        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (Schema::hasColumn('users', 'phone'))     $updateData['phone']     = $data['phone'] ?? null;
        if (Schema::hasColumn('users', 'address'))   $updateData['address']   = $data['address'] ?? null;
        if (Schema::hasColumn('users', 'branch'))    $updateData['branch']    = $data['branch'] ?? null;
        if (Schema::hasColumn('users', 'join_date')) $updateData['join_date'] = $data['join_date'] ?? null;

        if ($request->hasFile('photo') && Schema::hasColumn('users', 'profile_photo')) {
            if ($admin->profile_photo) {
                Storage::disk('public')->delete($admin->profile_photo);
            }
            $updateData['profile_photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $admin->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil diperbarui.',
            'user'    => [
                'id'       => $admin->id,
                'name'     => $admin->name,
                'email'    => $admin->email,
                'role'     => $admin->role,
                'phone'    => $admin->phone,
                'branch'   => $admin->branch,
                'is_active'=> $admin->is_active,
                'photo_url'=> $admin->photo_url,
                'initials' => $admin->initials,
            ],
        ]);
    }

    /* ─── TOGGLE ACTIVE (AJAX JSON) ──────────── */
    public function toggleActive(User $admin): JsonResponse
    {
        if ($admin->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menonaktifkan akun sendiri.'], 422);
        }

        if (!Schema::hasColumn('users', 'is_active')) {
            return response()->json(['success' => false, 'message' => 'Jalankan php artisan migrate terlebih dahulu.'], 422);
        }

        $admin->update(['is_active' => !$admin->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $admin->is_active,
            'message'   => 'Status akun diperbarui: '.($admin->is_active ? 'Aktif' : 'NonAktif'),
        ]);
    }

    /* ─── DESTROY (AJAX JSON) ─────────────────── */
    public function destroy(User $admin): JsonResponse
    {
        if ($admin->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        if ($admin->profile_photo) {
            Storage::disk('public')->delete($admin->profile_photo);
        }
        $admin->delete();

        return response()->json(['success' => true, 'message' => 'Akun berhasil dihapus.']);
    }
}
