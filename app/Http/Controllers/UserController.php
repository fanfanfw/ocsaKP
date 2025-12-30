<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(15);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'name' => ['nullable', 'string', 'max:150'],
            'role' => ['required', 'in:admin,tentor'],
            'password' => ['required', 'string', 'min:4'],
        ]);

        User::create([
            'username' => $validated['username'],
            'name' => $validated['name'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $user->id],
            'name' => ['nullable', 'string', 'max:150'],
            'role' => ['required', 'in:admin,tentor'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        $payload = [
            'username' => $validated['username'],
            'name' => $validated['name'] ?? null,
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->withErrors([
                'user' => 'Tidak bisa menghapus akun sendiri.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
