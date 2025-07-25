<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KaryawanController;

Route::resource('karyawan', KaryawanController::class);
Route::get('/', [AbsensiController::class, 'dashboard'])->name('dashboard');
Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
Route::post('/import', [AbsensiController::class, 'import'])->name('absensi.import');

// Tambahkan rute ini untuk menghapus semua data
Route::delete('/absensi/hapus-semua', [AbsensiController::class, 'destroyAll'])->name('absensi.destroyAll');