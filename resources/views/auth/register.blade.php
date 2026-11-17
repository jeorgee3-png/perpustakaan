@extends('layouts.app')

@section('title', 'Daftar Siswa - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="auth-wrapper">
        <div class="card">
            <div class="card-body" style="padding: 28px;">
                <h1 style="margin: 0 0 6px; font-size: 1.45rem;">Pendaftaran Anggota Siswa</h1>
                <p style="margin: 0 0 20px; font-size: 0.9rem; color: var(--ink-muted);">
                    Buat akun untuk meminjam buku dari katalog perpustakaan.
                </p>

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control"
                            required
                            autofocus
                            placeholder="Contoh: Dimas Pratama"
                        >
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control"
                            required
                            placeholder="nama@sekolah.sch.id"
                        >
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input
                            type="text"
                            id="kelas"
                            name="kelas"
                            value="{{ old('kelas') }}"
                            class="form-control"
                            required
                            placeholder="Contoh: XII RPL 2"
                        >
                        @error('kelas')
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
                            placeholder="Minimal 6 karakter"
                        >
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            required
                            placeholder="Ulangi kata sandi"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        Daftar &amp; Masuk
                    </button>
                </form>

                <p style="margin: 18px 0 0; text-align: center; font-size: 0.88rem; color: var(--ink-muted);">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 700;">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
@endsection
