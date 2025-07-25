<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->string('personnel_number');
            $table->string('personnel_name');
            $table->date('work_date'); // Tanggal kerja (mengikuti tanggal clock_in)
            $table->dateTime('clock_in')->nullable(); // Waktu masuk
            $table->dateTime('clock_out')->nullable(); // Waktu keluar
            $table->string('work_duration')->nullable(); // Durasi kerja
            $table->string('shift')->nullable(); // Shift 1, 2, atau 3
            $table->string('status_masuk')->nullable(); // Tepat Waktu atau Terlambat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};