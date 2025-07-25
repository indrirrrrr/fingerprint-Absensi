@extends('layouts.app')
@section('title', 'Master Data Karyawan')
@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-gradient-primary text-white">
        <h6 class="m-0 font-weight-bold">Daftar Karyawan</h6>
        <a href="{{ route('karyawan.create') }}" class="btn btn-light btn-sm"><i class="fas fa-plus fa-sm"></i> Tambah Karyawan</a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive" style="border-radius: 10px; overflow: hidden;">
            <table class="table table-bordered mb-0" width="100%" cellspacing="0">
                <thead class="text-white" style="background-color: #6c757d;">
                    <tr>
                        <th>No.</th>
                        <th>Nomor Karyawan</th>
                        <th>Nama Karyawan</th>
                        <th>Shift</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawan as $item)
                    <tr>
                        <td>{{ ($karyawan->currentPage() - 1) * $karyawan->perPage() + $loop->iteration }}</td>
                        <td>{{ $item->nomor_karyawan }}</td>
                        <td>{{ $item->nama_karyawan }}</td>
                        <td>{{ $item->shift }}</td>
                        <td>
                            <form action="{{ route('karyawan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data ini?')">
                                <a href="{{ route('karyawan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $karyawan->links() }}
        </div>
    </div>
</div>
@endsection