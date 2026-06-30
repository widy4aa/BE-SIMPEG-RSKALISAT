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
|               ⚠️ PENGINGAT PENTING (Jika akan kedaluwarsa dalam <= 90 hari).                     |
|               ℹ️ INFORMASI (Jika masa berlaku masih aktif > 90 hari).                             |
|               Milestone: 90, 60, 30, 21, 14, 7, 3, 2, 1 hari sebelum, hari H, 3 dan 7 hari setelah.|
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
|               4. SIP Sudah Kedaluwarsa < 0 hari (action_code: sip_expired)                        |
|               5. SIP Akan Segera Kedaluwarsa <= 90 hari lagi (action_code: sip_will_expire)       |
|               6. Penugasan Klinis Sudah Kedaluwarsa < 0 hari (action_code: penugasan_expired)     |
|               7. Penugasan Klinis Akan Kedaluwarsa <= 90 hari (action_code: penugasan_will_expire)|
|               8. Data Profil & Dokumen Belum Lengkap (action_code: profile_incomplete)            |
|                  *Mengecek NIK/NIP, Profesi, Tgl Masuk, Tgl Lahir, Alamat, KTP, KK, dll.*         |
|               9. Data Keluarga Belum Lengkap (action_code: keluarga_incomplete)                   |
|                  *Mengecek Buku Nikah, Data Pasangan/Anak/Orang Tua/Kontak Darurat.*              |
|               Threshold dokumen masa berlaku: 90, 60, 30, 21, 14, 7, 3, 2, 1 hari sebelum,        |
|               hari H, 3 hari sesudah, dan 7 hari sesudah. Lewat 7 hari sesudah akan resolved.     |
| 🎯 Kemana   : Ditampilkan di halaman Dashboard utama akun pegawai (sebagai banner/kartu aksi).    |
| 💡 Hasilnya : - Pegawai melihat daftar aksi (is_resolved = false) yang harus segera dilengkapi.   |
|               - SMART RESOLVE: Jika pegawai pergi ke menu profil/keluarga dan melengkapi datanya, |
|                 saat kembali ke dashboard, sistem otomatis mendeteksi data sudah lengkap dan      |
|                 menghapus/menandai notifikasi tersebut sebagai selesai (Resolved)!                |
+---------------------------------------------------------------------------------------------------+

+---------------------------------------------------------------------------------------------------+
| 📬 B. NOTIFIKASI INFORMASI (INFO NOTIFICATIONS - type: info)                                      |
+---------------------------------------------------------------------------------------------------+
| ⚡ Trigger  : Saat ini, notifikasi info BELUM memiliki trigger otomatis dari kode produksi.       |
|               Data notifikasi info hanya diisi melalui Seeder (PegawaiNotificationSeeder)         |
|               untuk keperluan pengujian dan demonstrasi.                                          |
|                                                                                                   |
|               ⚠️ TITIK TRIGGER YANG BELUM DIIMPLEMENTASI (kandidat pengembangan):                 |
|               1. Admin menyetujui/menolak Change Request profil pegawai                           |
|                  → Service: ChangeRequestAdminService::accept() / reject()                        |
|                  → Belum memanggil NotificationModel::create() type info                          |
|               2. HRD memverifikasi status kelayakan/validasi diklat                               |
|                  → Service: HrdDiklatStatusService::updateStatusKelayakan/Validasi()              |
|                  → Saat ini hanya mengirim WA, belum create notif in-app type info               |
|               3. Pegawai didaftarkan ke jadwal diklat baru                                        |
|                  → Belum ada trigger notif info                                                   |
|                                                                                                   |
| 🕒 Kapan    : Muncul di daftar kotak masuk notifikasi pegawai                                     |
|               (GET /api/notifications?type=info)                                                  |
| 🎯 Kemana   : Menu / Ikon lonceng notifikasi di pojok atas aplikasi pegawai.                      |
| 💡 Hasilnya : - Pegawai dapat melihat daftar pesan informasi yang belum dibaca (Unread).          |
|               - Pegawai dapat mengklik/menandai pesan tersebut agar berubah status menjadi        |
|                 "Sudah Dibaca" (Mark as Read via PATCH /api/notifications/{id}/read).             |
+---------------------------------------------------------------------------------------------------+
```

---

## 🏗️ Struktur & Endpoint Terkait

### Tabel `notification` (Model: `App\Models\NotificationModel`)
- `user_id`: ID user pemilik notifikasi.
- `type`: `info` (kotak masuk biasa) atau `action` (aksi dashboard).
- `title`, `message`: Judul dan isi pesan.
- `action_code`: Kode spesifik (`str_missing`, `str_will_expire`, `sip_expired`, `penugasan_will_expire`, `profile_incomplete`, dll.).
- `action_payload`: Payload JSON untuk data tambahan di frontend.
- `is_read`: Status apakah pesan sudah dibaca.
- `is_resolved`: Status apakah masalah/kewajiban sudah diselesaikan pegawai.
- `unique_key`: Kunci unik (contoh: `dashboard.str.missing`) untuk menjamin idempotensi.

### Endpoint API Notifikasi

| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/api/notifications?type=info` | Ambil notifikasi info yang belum dibaca |
| `GET` | `/api/notifications?type=action` | Ambil notifikasi action yang belum resolved |
| `PATCH` | `/api/notifications/{id}/read` | Tandai satu notifikasi sebagai sudah dibaca |
| `PATCH` | `/api/notifications/read-all` | Tandai semua notifikasi sebagai sudah dibaca |

**Query Parameter `type`:**

| Nilai | Default? | Keterangan |
|---|---|---|
| `info` | ✅ Ya | Mengembalikan notif jenis informasi yang `is_read = false` |
| `action` | Tidak | Mengembalikan notif jenis aksi yang `is_resolved = false` |

> Jika `?type` tidak dikirim, default ke `info` (backward compatible).
> Nilai selain `info` dan `action` akan menghasilkan respons **422 Unprocessable**.


---

## 📋 4. TABEL FITUR NOTIFIKASI YANG HARUS DIBUAT

Tabel di bawah memetakan seluruh fitur di sistem SIMPEG yang **seharusnya** memiliki notifikasi,
beserta mekanisme trigger-nya (**by action** = dipicu saat event API terjadi, **by cron** = dijadwalkan periodik).

Status: ✅ Sudah ada | ⚠️ Sebagian (WA only, belum in-app) | ❌ Belum ada

---

### 4.1 Notifikasi By Action (Event-Driven)

Trigger dipanggil langsung saat endpoint API dieksekusi.

> ⛔ **Kelayakan Diklat dikecualikan:** Event `HrdDiklatStatusService::updateStatusKelayakan()` tidak menghasilkan notifikasi apapun (WA maupun in-app) — by design.

| # | Fitur / Event | Penerima | Type | Judul Notif | Trigger di Service | WA | In-App | Status |
|---|---|---|---|---|---|---|---|---|
| 1 | Admin **approve** change request profil | Pegawai pengaju | `info` | "Perubahan data profil disetujui" | `ChangeRequestAdminService::accept()` | ✅ | ✅ | ✅ |
| 2 | Admin **reject** change request profil | Pegawai pengaju | `info` | "Perubahan data profil ditolak" | `ChangeRequestAdminService::reject()` | ✅ | ✅ | ✅ |
| 3 | HRD set validasi diklat → **valid** | Pegawai peserta | `info` | "Laporan diklat Anda telah divalidasi" | `HrdDiklatStatusService::updateStatusValidasi()` | ✅ | ✅ | ✅ |
| 4 | HRD set validasi diklat → **tidak valid** | Pegawai peserta | `info` | "Laporan diklat Anda ditolak" | `HrdDiklatStatusService::updateStatusValidasi()` | ✅ | ✅ | ✅ |
| 5 | HRD **mendaftarkan pegawai** ke jadwal diklat | Pegawai terdaftar | `info` | "Anda didaftarkan ke diklat baru" | `HrdDiklatPesertaService::syncPesertaDiklat()` | ✅ | ✅ | ✅ |
| 6 | Pegawai **submit change request** profil | Admin / HRD | `info` | "Ada pengajuan perubahan data baru" | `ProfileService::sendNotifCrSubmit()` | ✅ | ✅ | ✅ |
| 7 | Admin **buat akun pegawai baru** | Pegawai baru | `info` | "Selamat datang di SIMPEG RSKalisat" | `AdminPegawaiService::createPegawai()` | ✅ | ✅ | ✅ |
| 8 | Admin **ubah role / status** pegawai | Pegawai terkait | `info` | "Status akun Anda telah diperbarui" | `AdminPegawaiService::changeRole()` | ✅ | ✅ | ❌ |
| 9 | Admin input / perbarui **STR** pegawai | Pegawai terkait | `info` | "Data STR Anda diperbarui oleh admin" | `RiwayatKarir\Managed\StrService` (create / update) | ✅ | ✅ | ✅ |
| 10 | Admin input / perbarui **SIP** pegawai | Pegawai terkait | `info` | "Data SIP Anda diperbarui oleh admin" | `RiwayatKarir\Managed\SipService` (create / update) | ❌ | ✅ | ✅ |
| 11 | Admin input / perbarui **Penugasan Klinis** pegawai | Pegawai terkait | `info` | "Data Penugasan Klinis Anda diperbarui oleh admin" | `RiwayatKarir\Managed\PenugasanKlinisService` (create / update) | ✅ | ✅ | ❌ |
| 12 | Admin input / perbarui **Jabatan** pegawai | Pegawai terkait | `info` | "Data jabatan Anda diperbarui oleh admin" | `RiwayatKarir\Managed\JabatanService` (create / update) | ✅ | ✅ | ❌ |
| 13 | Admin input / perbarui **Pangkat** pegawai | Pegawai terkait | `info` | "Data pangkat Anda diperbarui oleh admin" | `RiwayatKarir\Managed\PangkatService` (create / update) | ✅ | ✅ | ❌ |
| 14 | Admin input / perbarui **Pendidikan** pegawai | Pegawai terkait | `info` | "Data pendidikan Anda diperbarui oleh admin" | `RiwayatKarir\Managed\PendidikanService` (create / update) | ✅ | ✅ | ❌ |

---

### 4.2 Notifikasi By Cron Job (Scheduled / Periodik)

Trigger dipanggil oleh scheduler Laravel secara otomatis pada waktu tertentu.

| # | Fitur / Kondisi | Penerima | Type | Judul Notif | `action_code` | Frekuensi | WA | In-App | Status |
|---|---|---|---|---|---|---|---|---|---|
| 1 | STR **belum tersedia** | Pegawai | `action` | "STR belum tersedia" | `str_missing` | Harian 01:00 | ❌ | ✅ | ✅ |
| 2 | STR **sudah kadaluarsa** (< 0 hari) | Pegawai | `action` | "STR sudah kadaluarsa" | `str_expired` | Harian 01:00 | ❌ | ✅ | ✅ |
| 3 | STR **akan kadaluarsa** (≤ 90 hari) | Pegawai | `action` | "STR akan segera kadaluarsa" | `str_will_expire` | Harian 01:00 | ❌ | ✅ | ✅ |
| 4 | Data **profil belum lengkap** | Pegawai | `action` | "Data profil belum lengkap" | `profile_incomplete` | Harian 01:00 | ❌ | ✅ | ✅ |
| 5 | Data **keluarga belum lengkap** | Pegawai | `action` | "Data keluarga belum lengkap" | `keluarga_incomplete` | Harian 01:00 | ❌ | ✅ | ✅ |
| 6 | SIP **sudah kadaluarsa** (< 0 hari) | Pegawai | `action` | "SIP sudah kadaluarsa" | `sip_expired` | Harian 01:00 | ❌ | ✅ | ✅ |
| 7 | SIP **akan kadaluarsa** (≤ 90 hari) | Pegawai | `action` | "SIP akan segera kadaluarsa" | `sip_will_expire` | Harian 01:00 | ❌ | ✅ | ✅ |
| 8 | Penugasan Klinis **sudah kadaluarsa** (< 0 hari) | Pegawai | `action` | "Penugasan Klinis sudah kadaluarsa" | `penugasan_expired` | Harian 01:00 | ❌ | ✅ | ✅ |
| 9 | Penugasan Klinis **akan kadaluarsa** (≤ 90 hari) | Pegawai | `action` | "Penugasan Klinis akan kadaluarsa" | `penugasan_will_expire` | Harian 01:00 | ❌ | ✅ | ✅ |
| 10 | Jadwal diklat **H-1** besok | Pegawai peserta | `info` | "Pengingat: Diklat Besok" | — | Harian 07:00 | ✅ | ✅ | ✅ |
| 10b | Diklat selesai **H+1** belum upload laporan/sertifikat | Pegawai peserta | `info` | "Segera Upload Laporan/Sertifikat Diklat" | — | Harian 07:05 | ✅ | ✅ | ✅ |
| 11 | Change request **pending > 3 hari** | Admin / HRD | `action` | "Ada pengajuan menunggu persetujuan" | `change_request_pending` | Harian 08:00 | ❌ | ✅ | ❌ |
| 12 | Diklat **menunggu verifikasi > 7 hari** | HRD | `action` | "Ada diklat menunggu verifikasi" | `diklat_pending_validasi` | Mingguan Senin 08:00 | ❌ | ✅ | ❌ |

Catatan threshold dokumen masa berlaku untuk STR, SIP, dan Penugasan Klinis:

- Action `*_will_expire` aktif saat `sisa_hari` berada pada rentang 1 sampai 90 hari sebelum kadaluarsa.
- Action `*_expired` aktif saat `sisa_hari` berada pada rentang 0 sampai -7 hari.
- Milestone payload: `90 hari sebelum`, `60 hari sebelum`, `30 hari sebelum`, `21 hari sebelum`, `14 hari sebelum`, `7 hari sebelum`, `3 hari sebelum`, `2 hari sebelum`, `1 hari sebelum`, `hari ini`, `3 hari sesudah`, `7 hari sesudah`.
- Jika dokumen sudah lewat lebih dari 7 hari setelah kadaluarsa, action tidak lagi masuk daftar aktif dan akan otomatis resolved oleh sync berikutnya.

---

### 4.3 Ringkasan Status

| Mekanisme | Total | ✅ Sudah Ada | ❌ Belum Ada |
|---|---|---|---|
| By Action | 14 | 9 | 5 |
| By Cron Job | 13 | 11 | 2 |
| **Total** | **27** | **20** | **7** |

> **Catatan item By Action yang belum ada (#7–#14):** Semua perlu notifikasi in-app `type: info` dan WhatsApp ke pegawai bersangkutan. Khusus item #7 (akun baru), pesan WA berisi kredensial login (username / NIK dan password sementara).

---

## 🔍 3. PERBEDAAN DETAIL: `info` vs `action`

### 3.1 Perbandingan Konsep

| Aspek | `info` | `action` |
|---|---|---|
| Makna | "Sesuatu sudah terjadi, kamu perlu tahu." | "Ada yang belum beres, kamu harus lakukan sesuatu." |
| Sifat | Pasif, satu arah | Aktif, dinamis |
| Cara selesai | User baca → `is_read = true` | Kondisi beres → `is_resolved = true` (otomatis) |
| Bisa duplikat? | Ya, tiap event buat baris baru | Tidak — pakai `upsert` via `unique_key` |
| Punya payload aksi? | Tidak | Ya (`action_code`, `action_payload`) |
| Auto-sync kondisi? | Tidak | Ya, via `NotificationActionSyncService` |
| Tampil di FE | Ikon lonceng / kotak masuk | Banner peringatan / to-do card di dashboard |

Kolom yang aktif dipakai masing-masing tipe:

```
Tipe info   → user_id, type, title, message, is_read
Tipe action → user_id, type, action_code, action_payload, is_resolved, unique_key, title, message, is_read
```

---

### 3.2 Contoh Kode: Notifikasi `info`

Notifikasi `info` dibuat secara manual dari kode saat sebuah event terjadi.
Tidak perlu `action_code`, `unique_key`, atau `action_payload` — cukup `create()`.

**Contoh 1: Perubahan data profil disetujui admin**

```php
NotificationModel::query()->create([
    'user_id' => $userId,
    'type'    => 'info',
    'title'   => 'Perubahan Data Disetujui',
    'message' => 'Perubahan data profil Anda telah disetujui oleh admin.',
    'is_read' => false,
]);
```

**Contoh 2: Pegawai didaftarkan ke diklat baru**

```php
NotificationModel::query()->create([
    'user_id' => $userId,
    'type'    => 'info',
    'title'   => 'Jadwal Diklat Baru',
    'message' => 'Anda telah didaftarkan pada diklat "Pelatihan BHD" tanggal 10 Juli 2026.',
    'is_read' => false,
]);
```

**Contoh 3: Perubahan data ditolak**

```php
NotificationModel::query()->create([
    'user_id' => $userId,
    'type'    => 'info',
    'title'   => 'Perubahan Data Ditolak',
    'message' => 'Perubahan data alamat Anda ditolak. Silakan hubungi HRD untuk informasi lebih lanjut.',
    'is_read' => false,
]);
```

**Lifecycle notifikasi `info`:**

```
1. Event terjadi (approve, reject, pendaftaran diklat, dll.)
        ↓
2. Kode memanggil NotificationModel::create()
        ↓
3. FE panggil GET /api/notifications → tampilkan notif is_read = false
        ↓
4. User klik notif → PATCH /api/notifications/{id}/read → is_read = true
        ↓
5. Notif hilang dari list (query hanya ambil is_read = false)
```

---

### 3.3 Contoh Kode: Notifikasi `action`

Notifikasi `action` dibuat via `NotificationRepository::upsertAction()` — menggunakan
`updateOrCreate` berbasis `unique_key` sehingga tidak pernah duplikat per user.

**Contoh 1: STR sudah kadaluarsa**

```php
$this->notificationRepository->upsertAction(
    userId:     $userId,
    uniqueKey:  'dashboard.str.expired',
    actionCode: 'str_expired',
    title:      'STR Sudah Kadaluarsa',
    message:    'STR sudah kadaluarsa 5 hari yang lalu. Segera perbarui.',
    payload: [
        'status_lengkap' => true,
        'sisa_hari'      => -5,
        'milestone_label' => '7 hari sesudah',
        'keterangan'     => ['STR sudah kadaluarsa sejak 5 hari lalu'],
    ],
);
```

**Contoh 2: Penugasan Klinis akan kadaluarsa**

```php
$this->notificationRepository->upsertAction(
    userId:     $userId,
    uniqueKey:  'dashboard.penugasan.will_expire',
    actionCode: 'penugasan_will_expire',
    title:      'Penugasan Klinis Akan Segera Kadaluarsa',
    message:    'Penugasan Klinis akan kadaluarsa dalam 21 hari. Segera lakukan perpanjangan.',
    payload: [
        'status_lengkap'  => true,
        'sisa_hari'       => 21,
        'milestone_label' => '21 hari sebelum',
        'keterangan'      => ['Penugasan Klinis akan kadaluarsa dalam 21 hari'],
    ],
);
```

**Contoh 3: Data profil belum lengkap**

```php
$this->notificationRepository->upsertAction(
    userId:     $userId,
    uniqueKey:  'dashboard.profile.incomplete',
    actionCode: 'profile_incomplete',
    title:      'Data Profil Belum Lengkap',
    message:    'Silakan lengkapi data profil pribadi dan dokumen Anda.',
    payload: [
        'status_lengkap' => false,
        'keterangan'     => ['Tanggal lahir belum terisi', 'Dokumen KTP belum diunggah'],
    ],
);
```

**Contoh 4: Data keluarga belum lengkap**

```php
$this->notificationRepository->upsertAction(
    userId:     $userId,
    uniqueKey:  'dashboard.keluarga.incomplete',
    actionCode: 'keluarga_incomplete',
    title:      'Data Keluarga Belum Lengkap',
    message:    'Silakan lengkapi data keluarga Anda.',
    payload: [
        'status_lengkap' => false,
        'keterangan'     => ['bukti pernikahan belum ada', 'data keluarga belum ada'],
    ],
);
```

**Lifecycle notifikasi `action`:**

```
1. User buka dashboard → syncDashboardActionsByUserId() dipanggil
        ↓
2. Service cek kondisi data pegawai (STR, SIP, Penugasan Klinis, profil, keluarga)
        ↓
        ├── Kondisi BERMASALAH → upsertAction() → is_resolved = false
        └── Kondisi SUDAH BERES → resolveActionsNotIn() → is_resolved = true
        ↓
3. FE baca notif action is_resolved = false → tampilkan sebagai banner/peringatan
        ↓
4. Pegawai ambil tindakan (perbarui STR/SIP/Penugasan Klinis, lengkapi profil, dll.)
        ↓
5. Sync berikutnya → kondisi terdeteksi beres → notif auto-resolve
```

---

### 3.4 Contoh Output API

**GET /api/notifications?type=info** — mengembalikan `info` yang `is_read = false`:

```json
{
    "success": true,
    "message": "Daftar notifikasi berhasil diambil.",
    "data": {
        "type": "info",
        "notifications": [
            {
                "id": 1,
                "title": "Perubahan Data Disetujui",
                "message": "Perubahan data profil Anda telah disetujui oleh admin.",
                "is_read": false,
                "created_at": "2026-04-20 10:00:00"
            },
            {
                "id": 2,
                "title": "Jadwal Diklat Baru",
                "message": "Anda telah didaftarkan pada diklat \"Pelatihan BHD\" tanggal 10 Juli 2026.",
                "is_read": false,
                "created_at": "2026-04-21 08:30:00"
            }
        ]
    }
}
```

**GET /api/notifications?type=action** — mengembalikan `action` yang `is_resolved = false`:

```json
{
    "success": true,
    "message": "Daftar notifikasi aksi berhasil diambil.",
    "data": {
        "type": "action",
        "notifications": [
            {
                "id": 5,
                "action_code": "str_expired",
                "action_payload": {
                    "status_lengkap": true,
                    "sisa_hari": -5,
                    "milestone_label": "7 hari sesudah",
                    "keterangan": ["STR sudah kadaluarsa sejak 5 hari lalu"]
                },
                "unique_key": "dashboard.str.expired",
                "title": "STR Sudah Kadaluarsa",
                "message": "STR sudah kadaluarsa 5 hari yang lalu. Segera perbarui.",
                "is_read": false,
                "is_resolved": false,
                "created_at": "2026-04-20 08:00:00"
            }
        ]
    }
}
```

**GET /api/notifications?type=invalid** — nilai tidak dikenal, response 422:

```json
{
    "success": false,
    "message": "Parameter type tidak valid. Gunakan: info atau action.",
    "data": null
}
```

---

### 3.5 Kapan Pakai Masing-Masing

**Gunakan `info` saat:**
1. Event sudah selesai terjadi (persetujuan, penolakan, pendaftaran diklat).
2. Tidak ada kondisi yang perlu terus dipantau — cukup dibaca sekali.
3. Notifikasi bersifat historis dan tidak akan berubah sendiri.

**Gunakan `action` saat:**
1. Ada kondisi data yang belum terpenuhi (STR mau expire, profil belum lengkap).
2. Perlu auto-resolve ketika kondisi sudah beres — tanpa intervensi manual.
3. Tidak boleh ada notif duplikat untuk kondisi yang sama (pakai `unique_key`).
4. FE perlu tahu detail aksi apa yang harus dilakukan pegawai (`action_code`, `action_payload`).
