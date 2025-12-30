<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f6f3;
            --card: #ffffff;
            --text: #1c2a23;
            --muted: #5f6b63;
            --primary: #1b7f5a;
            --primary-dark: #0f5d40;
            --border: #d9e2dc;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #edf5f1 0%, #f8faf8 35%, #f4f6f3 100%);
            color: var(--text);
            min-height: 100vh;
            padding: 24px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            width: min(420px, 100%);
            box-shadow: 0 10px 25px rgba(15, 40, 28, 0.08);
            margin: 0 auto;
        }
        h1 {
            margin: 0 0 6px;
            font-size: 22px;
        }
        p {
            margin: 0 0 18px;
            color: var(--muted);
            font-size: 14px;
        }
        label {
            font-weight: 600;
            font-size: 14px;
        }
        input, select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-top: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        .field {
            margin-bottom: 12px;
        }
        .btn {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn:hover {
            background: var(--primary-dark);
        }
        .btn-outline {
            width: 100%;
            background: transparent;
            color: var(--primary-dark);
            border: 1px solid var(--primary);
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }
        .password-field {
            position: relative;
        }
        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 36px;
            background: transparent;
            border: none;
            color: var(--muted);
            font-weight: 600;
            cursor: pointer;
        }
        .toggle-btn:focus {
            outline: 2px solid #b8e2d0;
            border-radius: 6px;
        }
        .error {
            background: #fdecec;
            border: 1px solid #f1bcbc;
            color: #9b2f2f;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Registrasi</h1>
        <p>Buat akun baru sebagai admin atau tentor.</p>
        @if($errors->register->any())
            <div class="error">{{ $errors->register->first() }}</div>
        @endif
        <form method="POST" action="{{ route('register.process') }}">
            @csrf
            <div class="field">
                <label for="register_username">Username</label>
                <input id="register_username" name="username" type="text" value="{{ old('username') }}" required>
            </div>
            <div class="field">
                <label for="register_name">Nama</label>
                <input id="register_name" name="name" type="text" value="{{ old('name') }}">
            </div>
            <div class="field">
                <label for="register_role">Role</label>
                <select id="register_role" name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    <option value="tentor" @selected(old('role') === 'tentor')>Tentor</option>
                </select>
            </div>
            <div class="field password-field">
                <label for="register_password">Kata sandi</label>
                <input id="register_password" name="password" type="password" required>
                <button class="toggle-btn" type="button" data-target="register_password">Lihat</button>
            </div>
            <div class="field password-field">
                <label for="register_password_confirmation">Ulangi kata sandi</label>
                <input id="register_password_confirmation" name="password_confirmation" type="password" required>
                <button class="toggle-btn" type="button" data-target="register_password_confirmation">Lihat</button>
            </div>
            <button class="btn" type="submit">Daftar</button>
        </form>
        <div style="margin-top:12px;">
            <a class="btn-outline" href="{{ route('login') }}">Kembali ke Login</a>
        </div>
    </div>
    <script>
        document.querySelectorAll('.toggle-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) {
                    return;
                }
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? 'Sembunyi' : 'Lihat';
            });
        });
    </script>
</body>
</html>
