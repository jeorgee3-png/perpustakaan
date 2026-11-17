@extends('layouts.app')

@section('title', $book->title . ' - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div style="margin-bottom: 16px;">
        <a href="{{ route('books.index') }}" class="btn btn-ghost btn-sm">&larr; Kembali ke Katalog</a>
    </div>

    <div class="detail-grid">
        <div class="card">
            <div class="book-cover book-cover-{{ $book->id % 5 }}" style="min-height: 170px;">
                <div class="book-cover-top">
                    <span class="book-year-pill">Terbit Tahun {{ $book->year }}</span>
                    @if ($book->stock > 0)
                        <span class="badge badge-success">Tersedia: {{ $book->stock }} Eksemplar</span>
                    @else
                        <span class="badge badge-danger">Stok Habis</span>
                    @endif
                </div>
                <h1 style="margin: 18px 0 0; font-size: 1.7rem; line-height: 1.25;">{{ $book->title }}</h1>
            </div>

            <div class="card-body">
                <h2 style="margin: 0 0 14px; font-size: 1.05rem;">Informasi Buku</h2>
                <table class="table">
                    <tbody>
                        <tr>
                            <th style="width: 180px;">Judul Buku</th>
                            <td><strong>{{ $book->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Penulis</th>
                            <td>{{ $book->author }}</td>
                        </tr>
                        <tr>
                            <th>Penerbit</th>
                            <td>{{ $book->publisher }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Terbit</th>
                            <td>{{ $book->year }}</td>
                        </tr>
                        <tr>
                            <th>Stok Tersedia</th>
                            <td>
                                @if ($book->stock > 0)
                                    <span class="badge badge-success">{{ $book->stock }} buku siap dipinjam</span>
                                @else
                                    <span class="badge badge-danger">0 buku (sedang kosong)</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Sedang Dipinjam</th>
                            <td>{{ $activeLoansCount }} eksemplar oleh anggota</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <aside class="card">
            <div class="card-body">
                <h2 style="margin: 0 0 10px; font-size: 1.1rem;">Peminjaman Buku</h2>
                <p style="margin: 0 0 14px; font-size: 0.9rem; color: var(--ink-muted);">
                    Ketentuan peminjaman di perpustakaan:
                </p>

                <ul style="margin: 0 0 18px; padding-left: 18px; font-size: 0.88rem; color: var(--ink-muted); display: grid; gap: 6px;">
                    <li>Durasi peminjaman: <strong>{{ \App\Models\Loan::LOAN_DAYS }} hari</strong>.</li>
                    <li>Maksimal buku aktif per anggota: <strong>{{ \App\Models\Loan::MAX_ACTIVE_LOANS }} buku</strong>.</li>
                    <li>Denda keterlambatan: <strong>Rp {{ number_format(\App\Models\Loan::DAILY_FINE, 0, ',', '.') }}/hari</strong>.</li>
                </ul>

                @auth
                    <div class="demo-box" style="margin-top: 0; margin-bottom: 16px;">
                        Status Peminjaman Anda: <strong>{{ $userActiveCount }} / {{ \App\Models\Loan::MAX_ACTIVE_LOANS }} buku aktif</strong>.
                    </div>

                    @if ($userActiveLoan)
                        <div class="alert alert-warning" style="margin-bottom: 14px;">
                            <span>
                                Anda sedang meminjam buku ini. Jatuh tempo:
                                <strong>{{ $userActiveLoan->due_date->format('d M Y') }}</strong>.
                            </span>
                        </div>
                        <form
                            action="{{ route('loans.return', $userActiveLoan) }}"
                            method="POST"
                            data-confirm="Kembalikan buku &quot;{{ $book->title }}&quot; sekarang?"
                            data-confirm-title="Konfirmasi Pengembalian"
                            data-confirm-label="Ya, Kembalikan"
                            data-confirm-variant="btn-success"
                        >
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg" style="width: 100%;">
                                Kembalikan Buku
                            </button>
                        </form>
                    @elseif ($book->stock < 1)
                        <button type="button" class="btn btn-primary btn-lg" style="width: 100%;" disabled>
                            Stok Buku Sedang Habis
                        </button>
                    @elseif ($userActiveCount >= \App\Models\Loan::MAX_ACTIVE_LOANS)
                        <button type="button" class="btn btn-primary btn-lg" style="width: 100%;" disabled>
                            Batas Maksimal 3 Buku Tercapai
                        </button>
                    @else
                        <form
                            action="{{ route('books.borrow', $book) }}"
                            method="POST"
                            data-confirm="Pinjam buku &quot;{{ $book->title }}&quot; hingga {{ now()->addDays(\App\Models\Loan::LOAN_DAYS)->format('d M Y') }}?"
                            data-confirm-title="Konfirmasi Peminjaman"
                            data-confirm-label="Pinjam Sekarang"
                        >
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                                Pinjam Buku Ini
                            </button>
                        </form>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <div style="margin-top: 12px; display: flex; gap: 8px;">
                            <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                                Edit Data Buku
                            </a>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg" style="width: 100%;">
                        Masuk untuk Meminjam
                    </a>
                    <p class="form-help" style="text-align: center; margin-top: 10px;">
                        Belum punya akun? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Daftar Siswa</a>
                    </p>
                @endauth
            </div>
        </aside>
    </div>

    @if(auth()->check() && auth()->user()->isAdmin() && $recentLoans->isNotEmpty())
        <section class="card" style="margin-top: 22px;">
            <div class="card-body">
                <h2 style="margin: 0 0 12px; font-size: 1.05rem;">Riwayat Peminjaman Terbaru Buku Ini</h2>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentLoans as $loan)
                                <tr>
                                    <td>{{ $loan->user->name }}</td>
                                    <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                    <td>{{ $loan->due_date->format('d M Y') }}</td>
                                    <td>{{ $loan->returned_at ? $loan->returned_at->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($loan->status === 'dikembalikan')
                                            <span class="badge badge-success">Dikembalikan</span>
                                        @elseif($loan->isOverdue())
                                            <span class="badge badge-danger">Terlambat</span>
                                        @else
                                            <span class="badge badge-warning">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    @endif
@endsection
