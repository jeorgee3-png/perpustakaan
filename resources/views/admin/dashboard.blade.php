@extends('layouts.app')

@section('title', 'Dashboard Admin - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Dashboard Admin Perpustakaan</h1>
            <p class="section-subtitle">Ringkasan inventaris buku, aktivitas peminjaman, dan pengelolaan anggota.</p>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary">+ Tambah Buku</a>
            <a href="{{ route('admin.loans.create') }}" class="btn btn-accent">+ Catat Peminjaman</a>
            <a href="{{ url('/admin') }}" class="btn btn-secondary">Panel Filament</a>
        </div>
    </div>

    <div class="stats-grid" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
        <div class="stat-card">
            <div class="stat-card-label">Total Judul Buku</div>
            <div class="stat-card-value">{{ number_format($stats['totalBooks']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Stok Tersedia</div>
            <div class="stat-card-value">{{ number_format($stats['totalStock']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Anggota Siswa</div>
            <div class="stat-card-value">{{ number_format($stats['totalStudents']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Sedang Dipinjam</div>
            <div class="stat-card-value">{{ number_format($stats['activeLoans']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Peminjaman Terlambat</div>
            <div class="stat-card-value" style="color: {{ $stats['overdueLoans'] > 0 ? 'var(--danger)' : 'inherit' }};">
                {{ number_format($stats['overdueLoans']) }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Denda Terkumpul</div>
            <div class="stat-card-value">Rp {{ number_format($stats['collectedFines'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="admin-grid">
        <section class="card">
            <div class="card-body">
                <div class="section-header" style="margin-bottom: 12px;">
                    <h2 style="margin: 0; font-size: 1.08rem;">Peminjaman Terbaru</h2>
                    <a href="{{ route('admin.loans.index') }}" class="btn btn-ghost btn-sm">Lihat Semua &rarr;</a>
                </div>

                @if ($recentLoans->isEmpty())
                    <div class="empty-state" style="padding: 24px 12px;">
                        <p>Belum ada transaksi peminjaman.</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Buku</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentLoans as $loan)
                                    <tr>
                                        <td>
                                            <strong>{{ $loan->user->name }}</strong>
                                            <div style="font-size: 0.78rem; color: var(--ink-muted);">{{ $loan->user->kelas ?? 'Admin' }}</div>
                                        </td>
                                        <td>{{ $loan->book->title }}</td>
                                        <td>{{ $loan->due_date->format('d M Y') }}</td>
                                        <td>
                                            @if ($loan->status === 'dikembalikan')
                                                <span class="badge badge-success">Dikembalikan</span>
                                            @elseif ($loan->isOverdue())
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
                @endif
            </div>
        </section>

        <section class="card">
            <div class="card-body">
                <div class="section-header" style="margin-bottom: 12px;">
                    <h2 style="margin: 0; font-size: 1.08rem;">Stok Buku Menipis (&le; 2)</h2>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-ghost btn-sm">Kelola Buku &rarr;</a>
                </div>

                @if ($lowStockBooks->isEmpty())
                    <div class="empty-state" style="padding: 24px 12px;">
                        <p>Semua stok buku dalam kondisi aman.</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Judul Buku</th>
                                    <th>Stok</th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStockBooks as $book)
                                    <tr>
                                        <td>
                                            <strong>{{ $book->title }}</strong>
                                            <div style="font-size: 0.78rem; color: var(--ink-muted);">{{ $book->author }}</div>
                                        </td>
                                        <td>
                                            @if ($book->stock === 0)
                                                <span class="badge badge-danger">Habis (0)</span>
                                            @else
                                                <span class="badge badge-warning">Sisa {{ $book->stock }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-secondary btn-sm">Ubah Stok</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
