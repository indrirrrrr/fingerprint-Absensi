# listener.py
import requests  # Library untuk mengirim data ke Laravel
from zk import ZK, const
import time

# --- KONFIGURASI ---
IP_MESIN = "192.168.97.201"
PORT_MESIN = 4370
# URL API di aplikasi Laravel untuk menerima satu data log baru
LARAVEL_API_URL = "http://127.0.0.1:8000/api/absensi/log-event"

# --- KODE UTAMA ---
zk = ZK(IP_MESIN, port=PORT_MESIN, timeout=60, force_udp=False)

while True:  # Loop ini akan berjalan selamanya untuk menjaga koneksi
    try:
        print(f"Mencoba terhubung ke mesin di {IP_MESIN}...")
        conn = zk.connect()
        print("\n>>> KONEKSI BERHASIL! Mendengarkan event absensi secara real-time...\n")

        # Ini adalah loop yang akan "menunggu" event baru dari mesin
        for attendance in conn.live_capture():
            if attendance is None:
                continue

            print("--- EVENT BARU DITERIMA! ---")
            print(f"  User ID  : {attendance.user_id}")
            print(f"  Waktu    : {attendance.timestamp}")
            
            # Format data yang akan dikirim ke Laravel
            payload = {
                'user_id': str(attendance.user_id),
                'timestamp': attendance.timestamp.strftime('%Y-%m-%d %H:%M:%S'),
            }

            # Kirim data ke Laravel menggunakan metode POST
            try:
                print(f"-> Mengirim data ke Laravel di {LARAVEL_API_URL}...")
                response = requests.post(LARAVEL_API_URL, json=payload, timeout=10)
                if response.status_code == 200:
                    print("   [SUCCESS] Data berhasil dikirim ke Laravel.\n")
                else:
                    print(f"   [ERROR] Laravel merespon dengan status {response.status_code}: {response.text}\n")
            except requests.exceptions.RequestException as e:
                print(f"   [ERROR] Gagal mengirim data ke Laravel: {e}\n")

    except Exception as e:
        print(f"Koneksi terputus atau gagal: {e}. Mencoba menghubungkan kembali dalam 10 detik...")
        if 'conn' in locals() and conn:
            conn.disconnect()
    
    time.sleep(10)