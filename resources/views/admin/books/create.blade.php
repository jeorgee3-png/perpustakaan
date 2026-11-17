@extends('layouts.app')

@section('title', 'Tambah Buku - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div style="max-width: 640px; margin: 0 auto;">
        <div class="section-header">
            <div>
                <h1 class="section-title">Tambah Buku Baru</h1>
                <p class="section-subtitle">Masukkan detail buku ke dalam katalog perpustakaan.</p>
            </div>
            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        </div>

        <div class="card">
            <div class="card-body" style="padding: 24px;">
                <form action="{{ route('admin.books.store') }}" method="POST">
                    @csrf
                    @include('admin.books._form')

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Buku</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
