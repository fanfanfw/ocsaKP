<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !$this->passwordMatches($user->password, $validated['password'])) {
            return back()->withErrors([
                'username' => 'Username atau kata sandi tidak sesuai.',
            ])->onlyInput('username');
        }

        if (!$this->isModernHash($user->password)) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function passwordMatches(string $storedHash, string $password): bool
    {
        if ($this->isModernHash($storedHash)) {
            return Hash::check($password, $storedHash);
        }

        if (hash_equals($storedHash, $password)) {
            return true;
        }

        $md5 = md5($password);

        if (hash_equals($storedHash, $md5)) {
            return true;
        }

        if (strlen($storedHash) === 16 && hash_equals($storedHash, substr($md5, 0, 16))) {
            return true;
        }

        return false;
    }

    private function isModernHash(string $hash): bool
    {
        return Str::startsWith($hash, ['$2y$', '$2a$', '$2b$', '$argon2i$', '$argon2id$']);
    }
}
