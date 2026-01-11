<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f6f3;
            --card: #ffffff;
            --text: #1c2a23;
            --muted: #5f6b63;
            --primary: #1b7f5a;
            --primary-dark: #0f5d40;
            --accent: #e2f2ec;
            --danger: #c93a3a;
            --border: #d9e2dc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #edf5f1 0%, #f8faf8 35%, #f4f6f3 100%);
            color: var(--text);
            min-height: 100vh;
        }

        header {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            background: #0f3b2a;
            color: #e8f5ef;
            padding: 24px 18px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .sidebar .brand {
            color: #e8f5ef;
        }

        .sidebar a {
            color: #e8f5ef;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            display: block;
            font-weight: 600;
            font-size: 14px;
        }

        .sidebar a:hover {
            background: rgba(232, 245, 239, 0.12);
        }

        .sidebar .user-info {
            color: #d3e7dd;
            font-size: 13px;
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar .badge {
            background: rgba(255, 255, 255, 0.16);
            color: #e8f5ef;
        }

        .content {
            flex: 1;
        }

        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand {
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 0.3px;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text);
            padding: 6px 10px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .nav-links a:hover {
            background: var(--accent);
        }

        .user-info {
            font-size: 13px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .container {
            max-width: 1200px;
            margin: 24px auto 60px;
            padding: 0 24px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(15, 40, 28, 0.06);
        }

        .grid {
            display: grid;
            gap: 16px;
        }

        .grid-4 {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table th,
        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        .table th {
            background: #f3f7f4;
            font-weight: 600;
            color: var(--muted);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--accent);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 600;
        }

        .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-dark);
            border: 1px solid var(--primary);
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            background: #a12f2f;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px;
        }

        input,
        select,
        textarea {
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-size: 14px;
            font-family: inherit;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
        }

        .alert-success {
            background: #e6f4ee;
            border: 1px solid #b8e2d0;
            color: #1f6b4b;
        }

        .alert-error {
            background: #fdecec;
            border: 1px solid #f1bcbc;
            color: #9b2f2f;
        }

        .pagination {
            margin-top: 16px;
        }

        @media (max-width: 720px) {
            .nav {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-info {
                width: 100%;
                justify-content: space-between;
            }

            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
            }

            .sidebar .user-info {
                width: 100%;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    @if(auth()->user()->role === 'admin')
        <div class="layout">
            <aside class="sidebar">
                <div class="brand">{{ config('app.name') }}</div>
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('assets.index') }}">Kelola Alat</a>
                <a href="{{ route('materi.index') }}">Kelola Materi</a>
                <a href="{{ route('jadwal.index') }}">Jadwal</a>
                <a href="{{ route('maintenance.index') }}">Perawatan</a>
                <a href="{{ route('reports.index') }}">Laporan</a>
                <div class="user-info">
                    <div>{{ auth()->user()->name ?? auth()->user()->username }}</div>
                    <span class="badge">{{ strtoupper(auth()->user()->role) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline" type="submit">Keluar</button>
                    </form>
                </div>
            </aside>
            <div class="content">
                <main class="container">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul style="margin: 0; padding-left: 18px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <header>
            <div class="nav">
                <div class="brand">{{ config('app.name') }}</div>
                <div class="nav-links">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('assets.index') }}">Data Alat</a>
                    <a href="{{ route('jadwal.index') }}">Jadwal</a>
                    <a href="{{ route('loans.index') }}">Pengembalian Alat</a>
                </div>
                <div class="user-info">
                    <span>{{ auth()->user()->name ?? auth()->user()->username }}</span>
                    <span class="badge">{{ strtoupper(auth()->user()->role) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline" type="submit">Keluar</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    @endif
</body>

</html>