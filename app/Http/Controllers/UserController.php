<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $users = User::query()
            ->when($search !== '', fn ($query) => $query->whereAny(
                ['name', 'email', 'role', 'unit_kerja'],
                'like',
                "%{$search}%",
            ))
            ->latest()
            ->paginate(100)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('users.form', ['user' => new User]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        User::create($this->validated($request));

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        return view('users.form', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validated($request, $user);

        if ($this->wouldRemoveLastActiveAdmin($user, $validated)) {
            return back()->with('error', 'Minimal satu akun admin aktif harus tetap tersedia.');
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Pengguna diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(request()->user())) {
            return back()->with('error', 'Akun sendiri tidak dapat dihapus.');
        }

        if ($user->hasRole('admin') && $user->is_active && ! User::query()->whereKeyNot($user->getKey())->where('role', 'admin')->where('is_active', true)->exists()) {
            return back()->with('error', 'Admin aktif terakhir tidak dapat dihapus.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $rules = ['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255', 'unique:users,email,'.($user?->id ?? '')], 'role' => ['required', 'in:admin,pimpinan,penyuluh'], 'unit_kerja' => ['nullable', 'string', 'max:120'], 'is_active' => ['required', 'boolean']];
        if (! $user || $request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $request->validate($rules);
    }

    /** @param array<string, mixed> $attributes */
    private function wouldRemoveLastActiveAdmin(User $user, array $attributes): bool
    {
        if (! $user->hasRole('admin') || ! $user->is_active) {
            return false;
        }

        $removesActiveAdminAccess = $attributes['role'] !== 'admin' || ! (bool) $attributes['is_active'];

        return $removesActiveAdminAccess
            && ! User::query()->whereKeyNot($user->getKey())->where('role', 'admin')->where('is_active', true)->exists();
    }
}
