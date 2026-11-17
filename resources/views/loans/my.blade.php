@extends('layouts.app')

@section('title', 'Peminjaman Saya - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Peminjaman Saya</h1>
            <p class="section-subtitle">Pantau buku yang sedang Anda pinjam, jatuh tempo, dan riwayat pengembalian.</p>
        </div>
        <a href="{{ route('books.index') }}" class="btn btn-primary">+ Pinjam Buku Lainnya</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-label">Sedang Dipinjam</div>
            <div class="stat-card-value">{{ $summary['active'] }} / {{ \App\Models\Loan::MAX_ACTIVE_LOANS }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Terlambat</div>
            <div class="stat-card-value" style="color: {{ $summary['overdue'] > 0 ? 'var(--danger)' : 'inherit' }};">
                {{ $summary['overdue'] }}
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Sudah Dikembalikan</div>
            <div class="stat-card-value">{{ $summary['returned'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-label">Total Denda</div>
            <div class="stat-card-value">Rp {{ number_format($summary['totalFine'], 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="margin-bottom: 16px;">
        <div class="tabs">
            <a href="{{ route('loans.my') }}" class="tab-link {{ $filter === 'all' ? 'active' : '' }}">Semua</a>
            <a href="{{ route('loans.my', ['status' => 'dipinjam']) }}" class="tab-link {{ $filter === 'dipinjam' ? 'active' : '' }}">Sedang Dipinjam</a>
            <a href="{{ route('loans.my', ['status' => 'terlambat']) }}" class="tab-link {{ $filter === 'terlambat' ? 'active' : '' }}">Terlambat</a>
            <a href="{{ route('loans.my', ['status' => 'dikembalikan']) }}" class="tab-link {{ $filter === 'dikembalikan' ? 'active' : '' }}">Dikembalikan</a>
        </div>
    </div>

    <div class="card">
        @if ($loans->isEmpty())
            <div class="empty-state">
                <h3>Belum ada data peminjaman</h3>
                <p>Anda belum memiliki catatan peminjaman pada filter ini.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary btn-sm">Jelajahi Katalog Buku</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            @php
                                $estimatedFine = $loan->estimatedFine();
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('books.show', $loan->book) }}" style="font-weight: 700; color: var(--accent);">
                                        {{ $loan->book->title }}
                                    </a>
                                    <div style="font-size: 0.8rem; color: var(--ink-muted);">{{ $loan->book->author }}</div>
                                </td>
                                <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                <td>{{ $loan->due_date->format('d M Y') }}</td>
                                <td>{{ $loan->returned_at ? $loan->returned_at->format('d M Y') : '-' }}</td>
                                <td>
                                    @if ($loan->status === 'dikembalikan')
                                        <span class="badge badge-success">Dikembalikan</span>
                                    @elseif ($loan->isOverdue())
                                        <span class="badge badge-danger">Terlambat ({{ $loan->overdueDays() }} hari)</span>
                                    @else
                                        <span class="badge badge-warning">Dipinjam</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($estimatedFine > 0)
                                        <strong style="color: var(--danger);">Rp {{ number_format($estimatedFine, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    @if ($loan->status === 'dipinjam')
                                        <form
                                            action="{{ route('loans.return', $loan) }}"
                                            method="POST"
                                            style="margin: 0; display: inline-block;"
                                            data-confirm="Kembalikan buku &quot;{{ $loan->book->title }}&quot; sekarang?"
                                            data-confirm-title="Konfirmasi Pengembalian"
                                            data-confirm-label="Ya, Kembalikan"
                                            data-confirm-variant="btn-success"
                                        >
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                                        </form>
                                    @else
                                        <span style="font-size: 0.82rem; color: var(--ink-soft);">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{ $loans->links('partials.pagination') }}
@endsection
