@extends('layouts.app')

@section('title', 'Masuk - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="auth-wrapper">
        <div class="card">
            <div class="card-body" style="padding: 28px;">
                <h1 style="margin: 0 0 6px; font-size: 1.45rem;">Masuk ke Perpustakaan</h1>
                <p style="margin: 0 0 20px; font-size: 0.9rem; color: var(--ink-muted);">
                    Gunakan akun siswa atau admin untuk meminjam dan mengelola buku.
                </p>

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control"
                            required
                            autofocus
                            placeholder="nama@perpus.test"
                        >
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                            placeholder="Masukkan kata sandi"
                        >
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" style="font-size: 0.88rem; color: var(--ink-muted); cursor: pointer;">
                            Ingat sesi saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Masuk
                    </button>
                </form>

                <p style="margin: 18px 0 0; text-align: center; font-size: 0.88rem; color: var(--ink-muted);">
                    Belum memiliki akun siswa?
                    <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 700;">Daftar sekarang</a>
                </p>

                @if (app()->isLocal())
                    <div class="demo-box">
                        <strong>Akun Demo (Seeder Lokal):</strong><br>
                        Admin: <code>admin@perpus.test</code> / <code>password</code><br>
                        Siswa: <code>siswa@perpus.test</code> / <code>password</code>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
