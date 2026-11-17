<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Perpustakaan'))</title>
    <link rel="stylesheet" href="{{ asset('css/library.css') }}">
</head>
<body>
<div class="page-shell">
    <header class="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('books.index') }}" class="brand">
                <span class="brand-mark" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/>
                    </svg>
                </span>
                <span>
                    {{ config('app.name', 'Perpustakaan') }}
                    <span class="brand-sub">Sistem Peminjaman Buku</span>
                </span>
            </a>

            <button
                type="button"
                id="nav-toggle"
                class="nav-toggle"
                aria-expanded="false"
                aria-controls="navbar-menu"
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
                Menu
            </button>

            <div id="navbar-menu" class="navbar-menu">
                <nav class="nav-links" aria-label="Navigasi Utama">
                    <a href="{{ route('books.index') }}"
                       class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        Katalog Buku
                    </a>

                    @auth
                        <a href="{{ route('loans.my') }}"
                           class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                            Peminjaman Saya
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                Kelola Admin
                            </a>
                            <a href="{{ route('admin.books.index') }}"
                               class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                                Data Buku
                            </a>
                            <a href="{{ route('admin.loans.index') }}"
                               class="nav-link {{ request()->routeIs('admin.loans.*') ? 'active' : '' }}">
                                Data Peminjaman
                            </a>
                            <a href="{{ route('admin.users.index') }}"
                               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                Anggota
                            </a>
                        @endif
                    @endauth
                </nav>

                <div class="nav-actions">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="user-chip" title="Pengaturan Profil">
                            <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span>
                                <strong>{{ auth()->user()->name }}</strong>
                                <span style="color: var(--ink-soft);">
                                    ({{ auth()->user()->isAdmin() ? 'Admin' : (auth()->user()->kelas ?: 'Siswa') }})
                                </span>
                            </span>
                        </a>

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar Siswa</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="alert-close" data-dismiss-alert aria-label="Tutup">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" role="alert">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="alert-close" data-dismiss-alert aria-label="Tutup">&times;</button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning" role="alert">
                    <span>{{ session('warning') }}</span>
                    <button type="button" class="alert-close" data-dismiss-alert aria-label="Tutup">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <div>
                        <strong>Terjadi kesalahan pada input:</strong>
                        <ul style="margin: 6px 0 0 18px; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="alert-close" data-dismiss-alert aria-label="Tutup">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-inner">
            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Perpustakaan') }} &mdash; Aplikasi Peminjaman Buku Berbasis Laravel Blade</span>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}" style="color: var(--primary); font-weight: 600;">Buka Panel Filament &rarr;</a>
                @endif
            @endauth
        </div>
    </footer>
</div>

<div id="confirm-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title">
    <div class="modal-card">
        <h3 id="confirm-modal-title" class="modal-title">Konfirmasi Aksi</h3>
        <p id="confirm-modal-text" class="modal-text">Apakah Anda yakin?</p>
        <div class="modal-actions">
            <button type="button" id="confirm-modal-cancel" class="btn btn-secondary">Batal</button>
            <button type="button" id="confirm-modal-submit" class="btn btn-primary">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script src="{{ asset('js/library.js') }}"></script>
@stack('scripts')
</body>
</html>
