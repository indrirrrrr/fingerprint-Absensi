<?php

namespace App\Imports;

use App\Models\Absensi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsensiImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $groupedData = $rows->groupBy('personnel_number');

        foreach ($groupedData as $personnelNumber => $records) {
            $sortedRecords = $records->sortBy(function ($record) {
                $dateTime = Date::excelToDateTimeObject($record['start_date'])->setTime(
                    Date::excelToDateTimeObject($record['start_time'])->format('H'),
                    Date::excelToDateTimeObject($record['start_time'])->format('i'),
                    Date::excelToDateTimeObject($record['start_time'])->format('s')
                );
                return Carbon::instance($dateTime);
            });
            
            $clockInEvent = null;
            $personnelName = $records->first()['textpersonnel_number'];

            foreach ($sortedRecords as $record) {
                $eventType = strtolower($record['texttime_event_type']);
                
                if ($eventType == 'clock-in' && $clockInEvent === null) {
                    $clockInEvent = $record;
                } 
                elseif ($eventType == 'clock-out' && $clockInEvent !== null) {
                    $clockInDate = Date::excelToDateTimeObject($clockInEvent['start_date']);
                    $clockInTimeExcel = Date::excelToDateTimeObject($clockInEvent['start_time']);
                    $fullClockIn = Carbon::instance($clockInDate)->setTimeFrom($clockInTimeExcel);
                    $jamMasuk = $fullClockIn->format('H:i:s');
                    
                    // Tentukan Shift dan Batas Terlambat
                    if ($jamMasuk >= '05:00:00' && $jamMasuk < '13:00:00') {
                        $shift = '1';
                        $batas_terlambat = '08:00:00';
                    } elseif ($jamMasuk >= '13:00:00' && $jamMasuk < '18:00:00') {
                        $shift = '2';
                        $batas_terlambat = '14:00:00';
                    } else {
                        $shift = '3';
                        $batas_terlambat = '17:00:00';
                    }

                    // Tentukan Status Masuk
                    $statusMasuk = ($jamMasuk > $batas_terlambat) ? 'Terlambat' : 'Tepat Waktu';

                    $clockOutDate = Date::excelToDateTimeObject($record['start_date']);
                    $clockOutTimeExcel = Date::excelToDateTimeObject($record['start_time']);
                    $fullClockOut = Carbon::instance($clockOutDate)->setTimeFrom($clockOutTimeExcel);
                    
                    $duration = $fullClockIn->diff($fullClockOut);
                    $workDuration = $duration->format('%H jam %i menit');

                    Absensi::create([
                        'personnel_number' => $personnelNumber,
                        'personnel_name' => $personnelName,
                        'work_date' => $fullClockIn->toDateString(),
                        'clock_in' => $fullClockIn,
                        'clock_out' => $fullClockOut,
                        'work_duration' => $workDuration,
                        'shift' => $shift,
                        'status_masuk' => $statusMasuk,
                    ]);

                    $clockInEvent = null;
                }
            }
        }
    }
}