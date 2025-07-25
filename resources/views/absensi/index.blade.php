@extends('layouts.app')

@section('title', 'Data Absensi Karyawan')

@section('content')

@if (session('success'))
    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-primary text-white">
        <h6 class="m-0 font-weight-bold">Data Absensi</h6>
        <form action="{{ route('absensi.destroyAll') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus semua data?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Semua Data"><i class="fas fa-trash"></i></button>
        </form>
    </div>
    <div class="card-body">
        
        <div class="table-responsive" style="border-radius: 10px; overflow: hidden;">
            <table class="table table-bordered mb-0" width="100%" cellspacing="0">
                <thead class="text-white" style="background-color: #6c757d;">
                    <tr>
                        <th>Nomor Karyawan</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal Kerja</th>
                        <th>Jadwal Kerja</th>
                        <th>Durasi</th>
                        <th>Shift</th>
                        <th>Status Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_absensi as $absensi)
                        <tr>
                            <td>{{ $absensi->personnel_number }}</td>
                            <td>{{ $absensi->personnel_name }}</td>
                            <td>{{ $absensi->work_date->format('d F Y') }}</td>
                            <td>
                                {{ $absensi->clock_in->format('H:i') }} - 
                                @if ($absensi->clock_out)
                                    {{ $absensi->clock_out->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $absensi->work_duration }}</td>
                            <td>{{ $absensi->shift }}</td>
                            <td>
                                <span class="text-{{ $absensi->status_masuk == 'Tepat Waktu' ? 'success' : 'danger' }} font-weight-bold">
                                    {{ $absensi->status_masuk }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Tidak ada data untuk ditampilkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $data_absensi->links() }}
        </div>
    </div>
</div>
@endsection