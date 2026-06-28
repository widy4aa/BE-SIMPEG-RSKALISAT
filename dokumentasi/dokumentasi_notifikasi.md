# Dokumentasi Notifikasi Aplikasi SIMPEG (WhatsApp & In-App)

Dokumen ini menjelaskan seluruh sistem notifikasi yang berjalan di aplikasi SIMPEG saat ini, mencakup **Notifikasi WhatsApp (WA Gateway Fonnte)** dan **Notifikasi Dalam Aplikasi (In-App)**, lengkap dengan pemicu (trigger), waktu pengiriman, tujuan, dan hasilnya dalam bentuk diagram box.

---

## 🟢 1. NOTIFIKASI WHATSAPP (WA)
Notifikasi WhatsApp dikirim secara langsung melalui integrasi API WhatsApp (Fonnte) ke nomor HP/WA pegawai yang terdaftar di sistem.

```
+---------------------------------------------------------------------------------------------------+
| 📱 1. RESET PASSWORD (OTP)                                                                        |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : Pegawai meminta kode OTP lupa password (POST /api/auth/forgot-password/request-otp).|
| 🕒 Kapan    : Dikirim secara REAL-TIME saat itu juga (dibatasi maksimal 1x per 60 detik).        |
| 🎯 Kemana   : Nomor WhatsApp pegawai (dari data no_telp di tabel pegawai_pribadi).                |
| 💡 Hasilnya : Pegawai menerima pesan WA berisi 6-digit kode OTP yang berlaku selama 5 menit.      |
|               Sistem merespons pesan sukses: "OTP berhasil dikirim ke nomor WhatsApp Anda."       |
+---------------------------------------------------------------------------------------------------+

+---------------------------------------------------------------------------------------------------+
| 📜 2. PENGINGAT KEDALUWARSA DOKUMEN (STR, SIP, & PENUGASAN KLINIS)                                |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : HRD menekan tombol "Kirim Pengingat WA" pada dokumen STR, SIP, atau Penugasan       |
|               Klinis milik pegawai di panel admin HRD.                                            |
| 🕒 Kapan    : Dikirim secara REAL-TIME setelah tombol ditekan oleh HRD.                           |
| 🎯 Kemana   : Nomor WhatsApp pegawai pemilik dokumen terkait (otomatis diformat ke prefix 62).    |
| 💡 Hasilnya : Pegawai menerima pesan peringatan dengan tingkat urgensi otomatis:                  |
|               🚨 SANGAT MENDESAK (Jika sudah kedaluwarsa / < 0 hari).                             |
|               ⚠️ PENGINGAT PENTING (Jika akan kedaluwarsa dalam <= 30 hari).                     |
|               ℹ️ INFORMASI (Jika masa berlaku masih aktif > 30 hari).                             |
|               *Disertai tautan link untuk meninjau/mengunduh dokumen lama.*                       |
+---------------------------------------------------------------------------------------------------+

+---------------------------------------------------------------------------------------------------+
| 🎓 3. HASIL VERIFIKASI DIKLAT (KELAYAKAN & VALIDASI)                                              |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : HRD mengubah status verifikasi diklat pegawai:                                      |
|               - Status Kelayakan (Diklat Eksternal): Layak / Tidak Layak                          |
|               - Status Validasi (Diklat Internal): Valid / Tidak Valid                            |
| 🕒 Kapan    : Dikirim secara REAL-TIME otomatis saat HRD menyimpan status verifikasi.             |
| 🎯 Kemana   : Nomor WhatsApp pegawai peserta diklat tersebut.                                     |
| 💡 Hasilnya : Pegawai langsung menerima pesan status verifikasi:                                  |
|               ✅ "Anda dinyatakan LAYAK mengikuti diklat..." / "Laporan Anda telah DIVALIDASI..." |
|               ❌ "Anda dinyatakan TIDAK LAYAK..." / "Laporan diklat Anda DITOLAK..."              |
+---------------------------------------------------------------------------------------------------+

+---------------------------------------------------------------------------------------------------+
| 💬 4. PESAN LANGSUNG KUSTOM (CUSTOM MESSAGE)                                                      |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : Admin / HRD mengirim pesan manual melalui form kirim pesan ke pegawai.              |
| 🕒 Kapan    : Dikirim secara REAL-TIME saat itu juga.                                             |
| 🎯 Kemana   : Nomor WhatsApp pegawai yang dipilih.                                                |
| 💡 Hasilnya : Pegawai menerima pesan teks persis sesuai yang diketik oleh Admin/HRD.               |
+---------------------------------------------------------------------------------------------------+
```

---

## 🔵 2. NOTIFIKASI BIASA (IN-APP / DI DALAM APLIKASI)
Notifikasi biasa disimpan di dalam database (tabel `notification`) dan ditampilkan langsung pada antarmuka aplikasi (Dashboard & Menu Notifikasi) saat pegawai login.

```
+---------------------------------------------------------------------------------------------------+
| 🔔 A. NOTIFIKASI AKSI / PERHATIAN (ACTION NOTIFICATIONS - type: action)                           |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : Sistem melakukan verifikasi otomatis (Auto-Sync) setiap kali pegawai membuka        |
|               Dashboard aplikasi (GET /api/dashboard) atau via Cron Scheduler (01:00 WIB).        |
| 🕒 Kapan    : Diperiksa & disinkronkan secara OTOMATIS oleh sistem. Poin yang diperiksa:          |
|               1. STR Belum Tersedia / Kosong (action_code: str_missing)                           |
|               2. STR Sudah Kedaluwarsa < 0 hari (action_code: str_expired)                        |
|               3. STR Akan Segera Kedaluwarsa <= 90 hari lagi (action_code: str_will_expire)       |
|               4. Data Profil & Dokumen Belum Lengkap (action_code: profile_incomplete)            |
|                  *Mengecek NIK/NIP, Profesi, Tgl Masuk, Tgl Lahir, Alamat, KTP, KK, dll.*         |
|               5. Data Keluarga Belum Lengkap (action_code: keluarga_incomplete)                   |
|                  *Mengecek Buku Nikah, Data Pasangan/Anak/Orang Tua/Kontak Darurat.*              |
| 🎯 Kemana   : Ditampilkan di halaman Dashboard utama akun pegawai (sebagai banner/kartu aksi).    |
| 💡 Hasilnya : - Pegawai melihat daftar aksi (is_resolved = false) yang harus segera dilengkapi.   |
|               - SMART RESOLVE: Jika pegawai pergi ke menu profil/keluarga dan melengkapi datanya, |
|                 saat kembali ke dashboard, sistem otomatis mendeteksi data sudah lengkap dan      |
|                 menghapus/menandai notifikasi tersebut sebagai selesai (Resolved)!                |
+---------------------------------------------------------------------------------------------------+

+---------------------------------------------------------------------------------------------------+
| 📬 B. NOTIFIKASI INFORMASI (INFO NOTIFICATIONS - type: info)                                      |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : Informasi umum dari sistem / riwayat pembaruan yang dikirimkan ke akun pegawai.     |
| 🕒 Kapan    : Muncul di daftar kotak masuk notifikasi pegawai (GET /api/notifications).           |
| 🎯 Kemana   : Menu / Ikon lonceng notifikasi di pojok atas aplikasi pegawai.                      |
| 💡 Hasilnya : - Pegawai dapat melihat daftar pesan informasi yang belum dibaca (Unread).          |
|               - Pegawai dapat mengklik/menandai pesan tersebut agar berubah status menjadi        |
|                 "Sudah Dibaca" (Mark as Read via PUT /api/notifications/{id}/read).              |
+---------------------------------------------------------------------------------------------------+
```

---

## 🏗️ Struktur & Endpoint Terkait

### Tabel `notification` (Model: `App\Models\NotificationModel`)
- `user_id`: ID user pemilik notifikasi.
- `type`: `info` (kotak masuk biasa) atau `action` (aksi dashboard).
- `title`, `message`: Judul dan isi pesan.
- `action_code`: Kode spesifik (`str_missing`, `profile_incomplete`, dll.).
- `action_payload`: Payload JSON untuk data tambahan di frontend.
- `is_read`: Status apakah pesan sudah dibaca.
- `is_resolved`: Status apakah masalah/kewajiban sudah diselesaikan pegawai.
- `unique_key`: Kunci unik (contoh: `dashboard.str.missing`) untuk menjamin idempotensi.

### Endpoint API Notifikasi
- `GET /api/notifications`: Mengambil daftar notifikasi info yang belum dibaca.
- `PUT /api/notifications/{id}/read`: Menandai satu notifikasi info sebagai sudah dibaca.
- `PUT /api/notifications/read-all`: Menandai seluruh notifikasi info sebagai sudah dibaca.
