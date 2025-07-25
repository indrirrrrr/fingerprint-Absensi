<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Imports\AbsensiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

// Tambahkan model Karyawan
use App\Models\Karyawan; // <-- Pastikan baris ini ada

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data statistik.
     */
    public function dashboard()
    {
        // == PERUBAHAN DI BARIS INI ==
        $total_karyawan = Karyawan::count(); 
        
        $hadir_hari_ini = Absensi::whereDate('work_date', Carbon::today())->distinct('personnel_number')->count();
        $event_hari_ini = Absensi::whereDate('work_date', Carbon::today())->count();
        
        return view('dashboard', [
            'total_karyawan' => $total_karyawan,
            'hadir_hari_ini' => $hadir_hari_ini,
            'event_hari_ini' => $event_hari_ini,
            'total_departemen' => 5 // Contoh data statis
        ]);
    }

    /**
     * Menampilkan halaman data absensi.
     */
    public function index()
    {
        $data_absensi = Absensi::orderBy('work_date', 'desc')
                               ->orderBy('clock_in', 'desc')
                               ->paginate(50);

        return view('absensi.index', compact('data_absensi'));
    }

    /**
     * Menangani import file Excel.
     */
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new AbsensiImport, $request->file('file'));
            return back()->with('success', 'Data absensi berhasil diimpor!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor file. Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus semua data absensi.
     */
    public function destroyAll()
    {
        Absensi::truncate();
        return back()->with('success', 'Semua data absensi berhasil dihapus.');
    }

    /**
     * Menerima data log dari mesin dan menyimpannya.
     */
    public function handleScan(Request $request)
    {
        $logs = $request->input('data', []);

        if (empty($logs)) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada data absensi baru yang ditemukan di mesin.'
            ]);
        }

        $log_dimasukkan = 0;

        foreach ($logs as $log) {
            if (!isset($log['user_id'])) continue;

            $karyawan = Karyawan::where('nomor_karyawan', $log['user_id'])->first();

            if ($karyawan) {
                $waktu_absen = Carbon::parse($log['timestamp']);

                $absen_ada = Absensi::where('nomor_karyawan', $karyawan->nomor_karyawan)
                                    ->where('clock_in', $waktu_absen->toDateTimeString())
                                    ->exists();

                if (!$absen_ada) {
                    Absensi::create([
                        'personnel_number' => $karyawan->nomor_karyawan,
                        'personnel_name'   => $karyawan->nama_karyawan,
                        'work_date'        => $waktu_absen->toDateString(),
                        'clock_in'         => $waktu_absen,
                        'status_masuk'     => 'Tepat Waktu',
                        'shift'            => $karyawan->shift ?? 1,
                        'work_duration'    => '0 menit',
                    ]);
                    $log_dimasukkan++;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Proses Selesai. $log_dimasukkan log absensi baru berhasil dimasukkan ke database."
        ]);
    }
}