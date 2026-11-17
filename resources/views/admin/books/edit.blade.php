@extends('layouts.app')

@section('title', 'Edit Buku - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div style="max-width: 640px; margin: 0 auto;">
        <div class="section-header">
            <div>
                <h1 class="section-title">Edit Data Buku</h1>
                <p class="section-subtitle">Perbarui informasi untuk "{{ $book->title }}".</p>
            </div>
            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">&larr; Kembali</a>
        </div>

        <div class="card">
            <div class="card-body" style="padding: 24px;">
                <form action="{{ route('admin.books.update', $book) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.books._form')

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
