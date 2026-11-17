@extends('layouts.app')

@section('title', 'Kelola Data Buku - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Kelola Data Buku</h1>
            <p class="section-subtitle">Tambah, perbarui, dan pantau ketersediaan stok koleksi buku.</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="btn btn-primary">+ Tambah Buku Baru</a>
    </div>

    <div class="filter-bar">
        <form action="{{ route('admin.books.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                class="form-control"
                style="flex: 1; min-width: 220px;"
                placeholder="Cari judul, penulis, atau penerbit..."
            >
            <button type="submit" class="btn btn-accent">Cari</button>
            @if ($search !== '')
                <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($books->isEmpty())
            <div class="empty-state">
                <h3>Belum ada data buku</h3>
                <p>Tambahkan buku pertama untuk mulai mengisi katalog perpustakaan.</p>
                <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm">+ Tambah Buku</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Dipinjam</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>
                                    <a href="{{ route('books.show', $book) }}" style="font-weight: 700; color: var(--accent);">
                                        {{ $book->title }}
                                    </a>
                                </td>
                                <td>{{ $book->author }}</td>
                                <td>{{ $book->publisher }}</td>
                                <td>{{ $book->year }}</td>
                                <td>
                                    @if ($book->stock > 0)
                                        <span class="badge badge-success">{{ $book->stock }}</span>
                                    @else
                                        <span class="badge badge-danger">0</span>
                                    @endif
                                </td>
                                <td>{{ $book->active_loans_count }}</td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form
                                            action="{{ route('admin.books.destroy', $book) }}"
                                            method="POST"
                                            style="margin: 0;"
                                            data-confirm="Hapus buku &quot;{{ $book->title }}&quot; secara permanen?"
                                            data-confirm-title="Hapus Buku"
                                            data-confirm-label="Ya, Hapus"
                                            data-confirm-variant="btn-danger"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{ $books->links('partials.pagination') }}
@endsection
