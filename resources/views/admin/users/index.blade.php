@extends('layouts.app')

@section('title', 'Data Anggota - ' . config('app.name', 'Perpustakaan'))

@section('content')
    <div class="section-header">
        <div>
            <h1 class="section-title">Daftar Anggota Perpustakaan</h1>
            <p class="section-subtitle">Lihat akun siswa dan admin beserta jumlah peminjaman masing-masing.</p>
        </div>
    </div>

    <div class="filter-bar">
        <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input
                type="text"
                name="q"
                value="{{ $search }}"
                class="form-control"
                style="flex: 1; min-width: 220px;"
                placeholder="Cari nama, email, atau kelas..."
            >
            <select name="role" class="form-control" style="width: 170px;">
                <option value="all" {{ $role === 'all' ? 'selected' : '' }}>Semua Peran</option>
                <option value="siswa" {{ $role === 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <button type="submit" class="btn btn-accent">Filter</button>
            @if ($search !== '' || $role !== 'all')
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        @if ($users->isEmpty())
            <div class="empty-state">
                <h3>Anggota tidak ditemukan</h3>
                <p>Tidak ada anggota yang sesuai dengan pencarian Anda.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Kelas</th>
                            <th>Sedang Dipinjam</th>
                            <th>Total Riwayat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $member)
                            <tr>
                                <td><strong>{{ $member->name }}</strong></td>
                                <td>{{ $member->email }}</td>
                                <td>
                                    @if ($member->isAdmin())
                                        <span class="badge badge-info">Admin</span>
                                    @else
                                        <span class="badge badge-success">Siswa</span>
                                    @endif
                                </td>
                                <td>{{ $member->kelas ?: '-' }}</td>
                                <td>{{ $member->active_loans_count }} / {{ \App\Models\Loan::MAX_ACTIVE_LOANS }}</td>
                                <td>{{ $member->total_loans_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{ $users->links('partials.pagination') }}
@endsection
