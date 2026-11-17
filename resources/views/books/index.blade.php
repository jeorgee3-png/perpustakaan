@extends('layouts.app')

@section('title', 'Katalog Buku - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <section class="hero">
        <div>
            <span class="hero-eyebrow">Perpustakaan Digital Sekolah</span>
            <h1>Jelajahi &amp; Pinjam Koleksi Buku dengan Mudah</h1>
            <p>
                Cari judul favorit Anda, cek ketersediaan stok secara langsung, dan kelola peminjaman hingga 3 buku aktif selama 7 hari.
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @auth
                    <a href="{{ route('loans.my') }}" class="btn btn-primary">
                        Lihat Peminjaman Saya ({{ $userActiveCount }}/3)
                    </a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.books.create') }}" class="btn btn-secondary">
                            + Tambah Buku Baru
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Masuk untuk Meminjam</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Daftar Akun Siswa</a>
                @endauth
            </div>
        </div>

        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-value">{{ number_format($stats['totalTitles']) }}</div>
                <div class="hero-stat-label">Judul Buku</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ number_format($stats['totalStock']) }}</div>
                <div class="hero-stat-label">Eksemplar Tersedia</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ number_format($stats['totalMembers']) }}</div>
                <div class="hero-stat-label">Anggota Siswa</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-value">{{ number_format($stats['activeLoans']) }}</div>
                <div class="hero-stat-label">Sedang Dipinjam</div>
            </div>
        </div>
    </section>

    <section class="filter-bar">
        <form action="{{ route('books.index') }}" method="GET" class="filter-grid">
            <div>
                <label for="q" class="form-label">Cari Buku</label>
                <input
                    type="text"
                    id="q"
                    name="q"
                    value="{{ $search }}"
                    class="form-control"
                    placeholder="Judul buku, penulis, atau penerbit..."
                >
            </div>

            <div>
                <label for="availability" class="form-label">Ketersediaan</label>
                <select id="availability" name="availability" class="form-control">
                    <option value="all" {{ $availability === 'all' ? 'selected' : '' }}>Semua Buku</option>
                    <option value="available" {{ $availability === 'available' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="empty" {{ $availability === 'empty' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            <div>
                <label for="sort" class="form-label">Urutkan</label>
                <select id="sort" name="sort" class="form-control">
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                    <option value="title_asc" {{ $sort === 'title_asc' ? 'selected' : '' }}>Judul (A - Z)</option>
                    <option value="year_desc" {{ $sort === 'year_desc' ? 'selected' : '' }}>Tahun Terbit Terbaru</option>
                    <option value="stock_desc" {{ $sort === 'stock_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-accent">Terapkan</button>
                @if($search !== '' || $availability !== 'all' || $sort !== 'latest')
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    <div class="section-header">
        <div>
            <h2 class="section-title">Katalog Koleksi Buku</h2>
            <p class="section-subtitle">
                Menampilkan {{ $books->count() }} dari {{ $books->total() }} buku
                @if($search !== '')
                    untuk pencarian "<strong>{{ $search }}</strong>"
                @endif
            </p>
        </div>
    </div>

    @if ($books->isEmpty())
        <div class="card">
            <div class="empty-state">
                <h3>Buku tidak ditemukan</h3>
                <p>Coba ubah kata kunci pencarian atau reset filter yang sedang aktif.</p>
                <a href="{{ route('books.index') }}" class="btn btn-secondary btn-sm">Lihat Semua Buku</a>
            </div>
        </div>
    @else
        <div class="books-grid">
            @foreach ($books as $book)
                @php
                    $coverTheme = $book->id % 5;
                    $isBorrowedByMe = in_array($book->id, $userActiveBookIds, true);
                    $reachedLimit = auth()->check() && $userActiveCount >= \App\Models\Loan::MAX_ACTIVE_LOANS;
                @endphp
                <article class="book-card">
                    <a href="{{ route('books.show', $book) }}" class="book-cover book-cover-{{ $coverTheme }}">
                        <div class="book-cover-top">
                            <span class="book-year-pill">{{ $book->year }}</span>
                            @if ($book->stock > 0)
                                <span class="badge badge-success">Stok: {{ $book->stock }}</span>
                            @else
                                <span class="badge badge-danger">Stok Habis</span>
                            @endif
                        </div>
                        <h3 class="book-cover-title">{{ $book->title }}</h3>
                    </a>

                    <div class="book-body">
                        <div class="book-meta">
                            <div>Penulis: <strong>{{ $book->author }}</strong></div>
                            <div>Penerbit: <strong>{{ $book->publisher }}</strong></div>
                        </div>

                        <div class="book-footer">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-secondary btn-sm">Detail</a>

                            @auth
                                @if ($isBorrowedByMe)
                                    <span class="badge badge-info">Sedang Anda Pinjam</span>
                                @elseif ($book->stock < 1)
                                    <button type="button" class="btn btn-primary btn-sm" disabled>Habis</button>
                                @elseif ($reachedLimit)
                                    <button type="button" class="btn btn-primary btn-sm" disabled title="Batas maksimal 3 buku aktif">Batas 3 Buku</button>
                                @else
                                    <form
                                        action="{{ route('books.borrow', $book) }}"
                                        method="POST"
                                        style="margin: 0;"
                                        data-confirm="Pinjam buku &quot;{{ $book->title }}&quot; selama {{ \App\Models\Loan::LOAN_DAYS }} hari?"
                                        data-confirm-title="Konfirmasi Peminjaman"
                                        data-confirm-label="Pinjam Sekarang"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm">Pinjam Buku</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk &amp; Pinjam</a>
                            @endauth
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{ $books->links('partials.pagination') }}
    @endif
@endsection
