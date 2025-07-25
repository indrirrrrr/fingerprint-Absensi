# bridge_app.py
from flask import Flask, jsonify
from flask_cors import CORS
from zk import ZK, const
# --- PERBAIKAN IMPORT ---
# Kita hanya mengimpor error yang pasti ada di library Anda
from zk.exception import ZKNetworkError, ZKError

app = Flask(__name__)
CORS(app)

# --- KONFIGURASI MESIN ---
# Pastikan alamat IP ini sudah benar dan bisa di-ping dari komputer Anda.
IP_MESIN = "192.168.99.201"
PORT_MESIN = 4370

@app.route('/get_attendance', methods=['GET'])
def tarik_data_absensi():
    """
    Endpoint untuk terhubung ke mesin absensi, menarik data log,
    dan mengirimkannya sebagai JSON.
    """
    print("\n[INFO] Menerima permintaan untuk menarik data dari mesin...")
    conn = None
    zk = ZK(IP_MESIN, port=PORT_MESIN, timeout=10, force_udp=False)

    try:
        # 1. Buat koneksi ke mesin
        print(f"[INFO] Mencoba terhubung ke mesin di {IP_MESIN}:{PORT_MESIN}...")
        conn = zk.connect()
        print(f"[SUCCESS] Berhasil terhubung ke mesin!")

        # 2. Tarik semua data log absensi dari mesin
        print("[INFO] Menarik data log absensi...")
        log_absensi = conn.get_attendance()
        print(f"[SUCCESS] Ditemukan {len(log_absensi)} data log.")

        # 3. Format data agar bisa dikirim sebagai JSON
        data_terformat = []
        if not log_absensi:
            print("[INFO] Tidak ada data absensi baru untuk diproses.")
        else:
            for absensi in log_absensi:
                status_text = "Check In"
                if absensi.status == const.ATT_STATE_CHECK_OUT:
                    status_text = "Check Out"
                # Anda bisa menambahkan status lain jika perlu
                
                data_terformat.append({
                    'user_id': absensi.user_id,
                    'timestamp': absensi.timestamp.strftime('%Y-%m-%d %H:%M:%S'),
                    'status': status_text,
                    'punch': absensi.punch
                })
            
            # 4. Hapus data log di mesin (OPSIONAL, HATI-HATI!)
            # conn.clear_attendance()
            # print("[INFO] Log absensi di mesin telah dibersihkan.")

        return jsonify({'success': True, 'data': data_terformat})

    # --- PERBAIKAN BLOK ERROR ---
    except ZKNetworkError as e:
        # Error ini terjadi jika koneksi jaringan gagal (misal: ping gagal)
        print(f"[ERROR] Gagal terhubung karena masalah jaringan: {e}")
        return jsonify({'success': False, 'message': f"Masalah Jaringan: Tidak bisa terhubung ke mesin di {IP_MESIN}. Pastikan mesin menyala dan kabel LAN terpasang dengan benar."}), 500
    except ZKError as e:
        # Error ini menangkap semua kesalahan lain dari library ZK
        print(f"[ERROR] Terjadi kesalahan pada library ZK: {e}")
        return jsonify({'success': False, 'message': f"Error dari mesin atau library: {e}"}), 500
    except Exception as e:
        # Menangkap semua error lain yang tidak terduga
        print(f"[ERROR] Terjadi kesalahan tidak terduga: {e}")
        return jsonify({'success': False, 'message': f"Error tidak terduga: {e}"}), 500
    finally:
        # 5. Selalu putuskan koneksi jika sudah selesai
        if conn and conn.is_connect:
            conn.disconnect()
            print("[INFO] Koneksi ke mesin diputus.")

if __name__ == '__main__':
    # Menjalankan server di semua alamat IP yang tersedia di komputer pada port 9000
    app.run(host='0.0.0.0', port=9000, debug=True)
