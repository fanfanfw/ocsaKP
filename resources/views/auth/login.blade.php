<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ config('app.name') }}</title>
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
            display: grid;
            place-items: center;
            padding: 24px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            width: min(420px, 100%);
            box-shadow: 0 10px 25px rgba(15, 40, 28, 0.08);
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
        input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            margin-top: 6px;
            font-size: 14px;
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
        <h1>Masuk</h1>
        <p>Gunakan akun admin atau tentor untuk mengakses sistem.</p>
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required>
            </div>
            <div class="field">
                <label for="password">Kata sandi</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button class="btn" type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>
