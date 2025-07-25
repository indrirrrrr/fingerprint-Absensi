<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AttendanceLog;
use Throwable;

class FetchAttendanceData extends Command
{
    /**
     * Signature dan deskripsi command.
     */
    protected $signature = 'app:fetch-attendance';
    protected $description = 'Fetch attendance data from the fingerprint machine bridge API';

    /**
     * Logika utama command.
     */
    public function handle()
    {
        $this->info('Starting to fetch attendance data...');

        // URL API dari bridge_app.py Anda
        // Pastikan IP dan port sudah benar
        $bridgeUrl = 'http://192.168.10.2:9000/get_attendance';

        try {
            // Lakukan request ke API Python dengan timeout 30 detik
            $response = Http::timeout(30)->get($bridgeUrl);

            // Jika request berhasil (HTTP status 200 OK)
            if ($response->successful()) {
                $data = $response->json();

                // Pastikan response dari API sukses dan ada data
                if ($data && $data['success'] && !empty($data['data'])) {
                    $this->info(count($data['data']) . ' records found. Processing...');
                    $newRecords = 0;

                    foreach ($data['data'] as $log) {
                        // Gunakan updateOrCreate untuk menghindari duplikasi data
                        $record = AttendanceLog::updateOrCreate(
                            [
                                'user_id' => $log['user_id'],
                                'timestamp' => $log['timestamp'],
                            ],
                            [
                                'status' => $log['status'],
                                'punch' => $log['punch'],
                            ]
                        );

                        // Cek apakah record baru dibuat
                        if($record->wasRecentlyCreated){
                            $newRecords++;
                        }
                    }

                    $this->info("Processing complete. {$newRecords} new records saved.");
                    Log::info('Attendance data fetched successfully. ' . $newRecords . ' new records.');

                } else {
                    $this->info('No new attendance data to process.');
                    Log::info('No new attendance data found from bridge.');
                }
            } else {
                // Jika request gagal (misal: 404 Not Found, 500 Internal Server Error)
                $this->error('Failed to connect to the bridge API. Status: ' . $response->status());
                Log::error('Failed to fetch attendance data. HTTP Status: ' . $response->status());
            }
        } catch (Throwable $e) {
            // Jika terjadi error koneksi (misal: API Python tidak aktif)
            $this->error('Connection error: ' . $e->getMessage());
            Log::error('Could not connect to the bridge API: ' . $e->getMessage());
        }

        return 0; // Command selesai dengan sukses
    }
}