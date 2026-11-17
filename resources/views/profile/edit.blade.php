@extends('layouts.app')

@section('title', 'Profil Saya - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div style="max-width: 620px; margin: 0 auto;">
        <div class="section-header">
            <div>
                <h1 class="section-title">Profil Saya</h1>
                <p class="section-subtitle">Perbarui informasi akun dan kata sandi Anda.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding: 24px;">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" id="kelas" name="kelas" value="{{ old('kelas', $user->kelas) }}" class="form-control" placeholder="Opsional untuk admin">
                        @error('kelas')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr style="border: 0; border-top: 1px solid var(--border); margin: 20px 0;">

                    <div class="form-group">
                        <label for="password" class="form-label">Kata Sandi Baru (Opsional)</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
