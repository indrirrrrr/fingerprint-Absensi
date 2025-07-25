<?php

namespace App\Models; // <-- PASTIKAN INI BENAR

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Karyawan
 * @package App\Models
 * * @property int $id
 * @property string $nomor_karyawan
 * @property string $nama_karyawan
 * @property int $shift
 * @property string|null $fingerprint_template
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Karyawan extends Model //
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     * @var string
     */
    protected $table = 'karyawans';

    /**
     * Kolom yang bisa diisi secara massal.
     * @var array
     */
    protected $fillable = [
        'nomor_karyawan',
        'nama_karyawan',
        'shift',
        'fingerprint_template',
    ];
}