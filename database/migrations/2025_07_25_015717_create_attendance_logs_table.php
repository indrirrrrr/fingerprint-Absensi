<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // ID pengguna dari mesin
            $table->dateTime('timestamp'); // Waktu absensi
            $table->string('status'); // "Check In" atau "Check Out"
            $table->integer('punch'); // Tipe punch dari mesin
            $table->timestamps(); // created_at dan updated_at

            // Mencegah duplikasi data
            $table->unique(['user_id', 'timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};