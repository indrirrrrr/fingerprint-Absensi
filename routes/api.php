// routes/api.php
use App\Http\Controllers\AbsensiController; // Sesuaikan dengan nama Controller Anda
use Illuminate\Support\Facades\Route;

Route::post('/absensi/scan', [AbsensiController::class, 'handleScan']);