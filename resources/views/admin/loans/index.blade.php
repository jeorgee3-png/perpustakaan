@extends('layouts.app')

@section('title', 'Kelola Peminjaman - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Kelola Data Peminjaman</h1>
            <p class="section-subtitle">Pantau seluruh peminjaman anggota, keterlambatan, dan proses pengembalian.</p>
        </div>
        <a href="{{ route('admin.loans.create') }}" class="btn btn-primary">+ Catat Peminjaman</a>
    </div>

    <div class="filter-bar">
        <form action="{{ route('admin.loans.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                class="form-control"
                style="flex: 1; min-width: 220px;"
                placeholder="Cari nama anggota, kelas, atau judul buku..."
            >

            <select name="status" class="form-control" style="width: 190px;">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="dipinjam" {{ $status === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                <option value="terlambat" {{ $status === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>

            <button type="submit" class="btn btn-accent">Filter</button>
            @if ($search !== '' || $status !== 'all')
                <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($loans->isEmpty())
            <div class="empty-state">
                <h3>Data peminjaman kosong</h3>
                <p>Tidak ada transaksi peminjaman yang cocok dengan filter Anda.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Anggota</th>
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
                                    <strong>{{ $loan->user->name }}</strong>
                                    <div style="font-size: 0.78rem; color: var(--ink-muted);">
                                        {{ $loan->user->kelas ?: $loan->user->email }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('books.show', $loan->book) }}" style="font-weight: 600; color: var(--accent);">
                                        {{ $loan->book->title }}
                                    </a>
                                </td>
                                <td>{{ $loan->loan_date->format('d M Y') }}</td>
                                <td>{{ $loan->due_date->format('d M Y') }}</td>
                                <td>{{ $loan->returned_at ? $loan->returned_at->format('d M Y') : '-' }}</td>
                                <td>
                                    @if ($loan->status === 'dikembalikan')
                                        <span class="badge badge-success">Dikembalikan</span>
                                    @elseif ($loan->isOverdue())
                                        <span class="badge badge-danger">Terlambat ({{ $loan->overdueDays() }} hr)</span>
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
                                            action="{{ route('admin.loans.return', $loan) }}"
                                            method="POST"
                                            style="margin: 0; display: inline-block;"
                                            data-confirm="Tandai buku &quot;{{ $loan->book->title }}&quot; milik {{ $loan->user->name }} sudah dikembalikan?"
                                            data-confirm-title="Konfirmasi Pengembalian"
                                            data-confirm-label="Kembalikan Buku"
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
