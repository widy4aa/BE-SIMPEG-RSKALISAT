# Rencana Implementasi Kustomisasi Template WhatsApp

Dokumen ini memuat blueprint (perencanaan) tentang bagaimana kita akan mengekstrak seluruh teks *hardcode* WhatsApp di dalam sistem agar bisa dikelola secara dinamis melalui database oleh HRD.

---

## 1. Daftar Template dan Key Database

Semua template akan disimpan di dalam tabel `settings`. Kita akan mendaftarkan 3 key utama:

| Key Database | Digunakan Pada Fitur | Placeholder (Variabel) yang Tersedia |
|---|---|---|
| `wa_template_dokumen_klinis` | Pengingat STR, SIP, & Penugasan Klinis (Otomatis & Manual) | `{nama}`, `{jenis_dokumen}`, `{nomor}`, `{tanggal_kadaluarsa}`, `{link_dokumen}` |
| `wa_template_diklat_h1` | Pengingat kehadiran Diklat H-1 (Otomatis) | `{nama}`, `{nama_diklat}`, `{tanggal_mulai}`, `{tempat}` |
| `wa_template_diklat_laporan` | Pengingat tagihan unggah sertifikat & laporan (Otomatis & Manual) | `{nama}`, `{nama_diklat}`, `{tanggal_selesai}`, `{label_dokumen}` |

---

## 2. Rencana Endpoint API (Khusus Admin)

Karena kebijakan menetapkan bahwa hanya **Admin** yang boleh mengubah tata bahasa pesan WhatsApp (bukan HRD), maka kita akan menempatkan *endpoint* ini di bawah perlindungan *middleware* khusus Admin.

Akan dibuat fungsi tambahan di `App\Http\Controllers\Api\Setting\SettingController` (atau *controller* baru di lingkup Admin) dengan Endpoint:
1. **`GET /api/settings/whatsapp-templates`**
   - Mengambil 3 teks template dari tabel `settings` beserta *default fallback*-nya jika belum pernah diset.
2. **`PUT /api/settings/whatsapp-templates`**
   - Menyimpan pembaruan teks form dari Frontend (layar Admin) ke tabel `settings`.
3. **`POST /api/settings/whatsapp-templates/preview`**
   - Endpoint simulasi/preview teks. Menerima `key` dan `teks_template` dari Frontend, lalu me-return teks jadi yang *placeholder*-nya sudah diganti dengan data contoh (*dummy data*), seperti `{nama}` menjadi "Budi Santoso", agar Admin bisa melihat langsung hasil jadinya sebelum disimpan.

---

## 3. Rencana Refactoring Kode (*Source Code*)

Teks yang saat ini diketik mati (seperti *"Halo Pegawai, besok ada diklat..."*) akan dicabut dan diganti menggunakan `Setting::where('key', '...')->value('value')` dan digabungkan menggunakan fungsi PHP `str_replace()`.

Berkas (*file*) yang akan dimodifikasi:
1. **`routes/console.php`**
   - Mengubah logika teks WhatsApp di dalam fungsi `notifications:diklat-reminder` (H-1).
   - Mengubah logika teks WhatsApp di dalam fungsi `notifications:diklat-laporan-reminder` (H+1 upload laporan).
2. **`app/Http/Controllers/Api/Hrd/HrdReminderController.php`**
   - Mengubah teks di dalam fungsi tembak manual *Reminder* STR, SIP, dan Penugasan Klinis.
3. **`app/Http/Controllers/Api/Diklat/Managed/DiklatController.php`**
   - Mengubah teks di dalam fungsi tembak manual *remindUploadLaporan*.
