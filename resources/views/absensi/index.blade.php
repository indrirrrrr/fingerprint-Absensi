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

        <div class="mb-4">
            <h5>Pemindaian Real-time</h5>
            <p class="text-muted small">Gunakan tombol di bawah untuk mengaktifkan/menonaktifkan penarikan data otomatis dari mesin absensi.</p>
            
            <button id="toggleScanBtn" class="btn btn-success btn-sm">
                <i class="fas fa-play"></i> Aktifkan Scan Otomatis
            </button>
            
            <div id="scanStatus" class="alert mt-3 d-none" role="alert"></div>
        </div>
        
        <hr>
        
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

@push('scripts')
<script>
// ... (Seluruh kode JavaScript tidak perlu diubah) ...
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleScanBtn');
    const scanStatus = document.getElementById('scanStatus');
    let pollingInterval;
    let isPolling = false;

    toggleBtn.addEventListener('click', function() {
        if (isPolling) {
            clearInterval(pollingInterval);
            isPolling = false;
            toggleBtn.classList.replace('btn-danger', 'btn-success');
            toggleBtn.innerHTML = '<i class="fas fa-play"></i> Aktifkan Scan Otomatis';
            updateStatus('warning', 'Pemindaian otomatis dihentikan.');
        } else {
            isPolling = true;
            toggleBtn.classList.replace('btn-success', 'btn-danger');
            toggleBtn.innerHTML = '<i class="fas fa-stop"></i> Hentikan Scan Otomatis';
            updateStatus('info', 'Pemindaian otomatis aktif... Mengecek data setiap 10 detik.');
            fetchAttendanceData();
            pollingInterval = setInterval(fetchAttendanceData, 10000);
        }
    });

    async function fetchAttendanceData() {
        scanStatus.textContent = 'Mengecek data baru dari mesin...';
        try {
            const responseFromBridge = await fetch('http://127.0.0.1:9000/get_attendance');
            if (!responseFromBridge.ok) throw new Error('Aplikasi Jembatan tidak merespon.');
            
            const dataFromBridge = await responseFromBridge.json();
            if (!dataFromBridge.success) throw new Error(dataFromBridge.message);

            if (dataFromBridge.data.length > 0) {
                scanStatus.textContent = `Menemukan ${dataFromBridge.data.length} log baru, mengirim ke server...`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const responseFromLaravel = await fetch('/api/absensi/scan', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                    body: JSON.stringify({ data: dataFromBridge.data })
                });

                const dataFromLaravel = await responseFromLaravel.json();
                if (!responseFromLaravel.ok) throw new Error(dataFromLaravel.message);

                updateStatus('success', `✅ ${dataFromLaravel.message}. Halaman akan dimuat ulang.`);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                scanStatus.textContent = 'Tidak ada data baru. Pengecekan selanjutnya dalam 10 detik.';
            }

        } catch (error) {
            updateStatus('danger', `❌ Error: ${error.message}`);
            clearInterval(pollingInterval);
            isPolling = false;
            toggleBtn.classList.replace('btn-danger', 'btn-success');
            toggleBtn.innerHTML = '<i class="fas fa-play"></i> Aktifkan Scan Otomatis';
        }
    }

    function updateStatus(type, message) {
        scanStatus.className = `alert alert-${type} mt-3`;
        scanStatus.textContent = message;
        scanStatus.classList.remove('d-none');
    }
});
</script>
@endpush