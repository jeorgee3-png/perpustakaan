@extends('layouts.app')

@section('title', 'Catat Peminjaman - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div style="max-width: 640px; margin: 0 auto;">
        <div class="section-header">
            <div>
                <h1 class="section-title">Catat Peminjaman Baru</h1>
                <p class="section-subtitle">Buat transaksi peminjaman buku untuk anggota perpustakaan.</p>
            </div>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        </div>

        <div class="card">
            <div class="card-body" style="padding: 24px;">
                <form action="{{ route('admin.loans.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="user_id" class="form-label">Anggota Peminjam</label>
                        <select id="user_id" name="user_id" class="form-control" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach ($users as $member)
                                <option value="{{ $member->id }}" {{ old('user_id') == $member->id ? 'selected' : '' }}>
                                    {{ $member->name }} ({{ $member->kelas ?: $member->role }} - {{ $member->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="book_id" class="form-label">Buku</label>
                        <select id="book_id" name="book_id" class="form-control" required>
                            <option value="">-- Pilih Buku Tersedia --</option>
                            @foreach ($books as $book)
                                <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                    {{ $book->title }} &mdash; {{ $book->author }} (Stok: {{ $book->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('book_id')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        <div class="form-group">
                            <label for="loan_date" class="form-label">Tanggal Pinjam</label>
                            <input
                                type="date"
                                id="loan_date"
                                name="loan_date"
                                value="{{ old('loan_date', now()->toDateString()) }}"
                                class="form-control"
                                required
                            >
                            @error('loan_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                            <input
                                type="date"
                                id="due_date"
                                name="due_date"
                                value="{{ old('due_date', now()->addDays(\App\Models\Loan::LOAN_DAYS)->toDateString()) }}"
                                class="form-control"
                                required
                            >
                            @error('due_date')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
