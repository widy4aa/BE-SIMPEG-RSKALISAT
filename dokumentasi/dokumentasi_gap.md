
# Dokumentasi GAP

## Ringkasan Temuan

Fokus: ketidakkonsistenan response antar fitur, mismatch doc vs implementasi, dan referensi middleware/route.

## Temuan Detail

### 1) Response Health API tidak konsisten (dokumentasi_app vs implementasi)

- **Dokumentasi** menampilkan field `service` dan `timestamp` pada response health.
	- Bukti: [dokumentasi/dokumentasi_app.md](dokumentasi/dokumentasi_app.md#L138-L145)
- **Implementasi** hanya mengembalikan `status`.
	- Bukti: [routes/api.php](routes/api.php#L209-L217)
- **Dampak**: integrator yang mengikuti dokumentasi_app akan berharap field tambahan yang tidak ada di response nyata.

### 2) Message Dashboard API tidak konsisten (dokumentasi_app vs implementasi)

- **Dokumentasi** memakai pesan statis `Dashboard berhasil diambil`.
	- Bukti: [dokumentasi/dokumentasi_app.md](dokumentasi/dokumentasi_app.md#L336)
- **Implementasi** memakai pesan dinamis dari payload (`$payload['welcome']`).
	- Bukti: [app/Http/Controllers/Api/DashboardController.php](app/Http/Controllers/Api/DashboardController.php#L33)
- **Dampak**: test/validasi yang mengunci string message akan gagal jika mengikuti dokumentasi_app.

### 3) Middleware di dokumentasi_app tidak sesuai implementasi routes

- **Dokumentasi** memakai alias middleware `jwt.auth` dan `role:...`.
	- Bukti: [dokumentasi/dokumentasi_app.md](dokumentasi/dokumentasi_app.md#L259)
	- Bukti tambahan: [dokumentasi/dokumentasi_app.md](dokumentasi/dokumentasi_app.md#L418)
- **Implementasi** memakai middleware class `JwtAuthMiddleware::class` dan `RoleMiddleware::class`.
	- Bukti: [routes/api.php](routes/api.php#L31)
- **Dampak**: pembaca dokumentasi_app bisa mengira middleware alias tersedia, padahal yang dipakai adalah class-based.

### 4) Role akses edit/delete Diklat tidak konsisten (dokumentasi_api vs routes)

- **Dokumentasi API** menyebut role edit/delete Diklat hanya `pegawai`.
	- Bukti edit: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L1054)
	- Bukti delete: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L1117)
- **Routes** mengizinkan `pegawai`, `hrd`, dan `direktur` untuk update/delete.
	- Bukti update: [routes/api.php](routes/api.php#L132)
	- Bukti delete: [routes/api.php](routes/api.php#L133)
- **Dampak**: konsumen API bisa salah membatasi akses role, atau sebaliknya menganggap akses tidak tersedia.

### 5) Ringkasan Data Keluarga tidak konsisten (dokumentasi_api vs implementasi)

- **Dokumentasi API** menyebut `data` berisi `pasangan`, `anak`, `orang_tua`, `kontak_darurat` (label/total/items) tanpa `total_keluarga`.
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2249-L2278)
- **Implementasi** mengembalikan `total_keluarga` dan `rincian` dengan struktur berbeda (`pasangan`, `anak`, `orang_tua`, `kontak_darurat`, `tanggungan_lain`).
	- Bukti: [app/Services/DataKeluarga/DataKeluargaService.php](app/Services/DataKeluarga/DataKeluargaService.php#L9-L47)
	- Bukti: [app/Http/Controllers/Api/DataKeluargaController.php](app/Http/Controllers/Api/DataKeluargaController.php#L15-L33)
- **Dampak**: frontend yang mengikuti dokumentasi_api akan gagal membaca struktur response nyata.

### 6) List Keluarga (Pasangan/Anak/Orang Tua/Kontak Darurat) tidak konsisten (dokumentasi_api vs implementasi)

- **Dokumentasi API**: response `data` berbentuk array item langsung.
	- Bukti pasangan: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2282-L2292)
	- Bukti anak: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2389-L2399)
	- Bukti orang tua: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2471-L2481)
	- Bukti kontak darurat: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2586-L2596)
- **Implementasi**: response `data` berupa object ringkasan `{ label, total, items }` dan `message` diambil dari `welcome` service.
	- Bukti pasangan: [app/Services/DataKeluarga/PasanganService.php](app/Services/DataKeluarga/PasanganService.php#L12-L33)
	- Bukti anak: [app/Services/DataKeluarga/AnakService.php](app/Services/DataKeluarga/AnakService.php#L12-L33)
	- Bukti orang tua: [app/Services/DataKeluarga/OrangTuaService.php](app/Services/DataKeluarga/OrangTuaService.php#L9-L25)
	- Bukti kontak darurat: [app/Services/DataKeluarga/KontakDaruratService.php](app/Services/DataKeluarga/KontakDaruratService.php#L9-L25)
- **Dampak**: struktur data list keluarga di dokumentasi_api tidak sesuai respons aktual.

### 7) Payload create/update Pasangan/Anak tidak konsisten (dokumentasi_api vs implementasi)

- **Dokumentasi API** menampilkan `buku_nikah_file_path` / `akta_kelahiran_file_path` di response create/update.
	- Bukti pasangan: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2322-L2330)
	- Bukti anak: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2427-L2435)
- **Implementasi** hanya mengembalikan `{ id, nama_lengkap }` untuk create/update pasangan/anak.
	- Bukti pasangan: [app/Services/DataKeluarga/PasanganService.php](app/Services/DataKeluarga/PasanganService.php#L55-L63)
	- Bukti anak: [app/Services/DataKeluarga/AnakService.php](app/Services/DataKeluarga/AnakService.php#L55-L63)
- **Dampak**: konsumen API yang mengandalkan field file path akan gagal.

### 8) Pesan response antar riwayat karir tidak konsisten

- **Pendidikan/Jabatan**: `Data riwayat ... berhasil diambil.`
	- Bukti pendidikan: [app/Http/Controllers/Api/RiwayatKarirController.php](app/Http/Controllers/Api/RiwayatKarirController.php#L22-L41)
	- Bukti jabatan: [app/Http/Controllers/Api/RiwayatKarirController.php](app/Http/Controllers/Api/RiwayatKarirController.php#L43-L62)
- **Pangkat/SIP/STR/Penugasan Klinis**: `Berhasil mengambil riwayat ...` (format berbeda).
	- Bukti pangkat: [app/Http/Controllers/Api/RiwayatKarirController.php](app/Http/Controllers/Api/RiwayatKarirController.php#L96-L108)
	- Bukti sip: [app/Http/Controllers/Api/RiwayatKarirController.php](app/Http/Controllers/Api/RiwayatKarirController.php#L146-L158)
	- Bukti str: [app/Http/Controllers/Api/RiwayatKarirController.php](app/Http/Controllers/Api/RiwayatKarirController.php#L189-L201)
	- Bukti penugasan klinis: [app/Http/Controllers/Api/RiwayatKarirController.php](app/Http/Controllers/Api/RiwayatKarirController.php#L232-L244)
- **Dampak**: konsumen yang membandingkan message antar endpoint serupa akan menemukan pola tidak konsisten.

### 9) Master Data tidak mengikuti format standar (tanpa `message`)

- **Implementasi** hanya mengembalikan `success` dan `data`.
	- Bukti: [app/Http/Controllers/Api/MasterDataController.php](app/Http/Controllers/Api/MasterDataController.php#L9-L62)
- **Format Response Standar** di dokumentasi_api menyebut adanya `message` untuk response sukses.
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L135-L146)
- **Dampak**: format response tidak seragam untuk endpoint master data.

### 10) Error format pada Keluarga tidak konsisten dengan endpoint lain

- **Keluarga Controllers** mengembalikan `500` dengan message `Terjadi kesalahan pada server: ...` ketika exception umum.
	- Bukti pasangan: [app/Http/Controllers/Api/Keluarga/PasanganController.php](app/Http/Controllers/Api/Keluarga/PasanganController.php#L20-L32)
	- Bukti anak: [app/Http/Controllers/Api/Keluarga/AnakController.php](app/Http/Controllers/Api/Keluarga/AnakController.php#L20-L32)
- **Endpoint lain** (misalnya Profile/Riwayat Karir) cenderung mengembalikan `422`/`404` dengan message tanpa detail internal.
	- Bukti profile update: [app/Http/Controllers/Api/ProfileController.php](app/Http/Controllers/Api/ProfileController.php#L36-L68)
- **Dampak**: format error dan potensi kebocoran detail exception tidak konsisten.

### 11) Change Request Admin response tidak konsisten dengan dokumentasi_api

- **List Change Request** di dokumentasi_api menampilkan `data` sebagai array item (ok), tetapi detail item di implementasi berisi `by_user.username`, `role`, `nama`, `note`, dan `created_at/updated_at`.
	- Bukti implementasi: [app/Services/ChangeRequest/ChangeRequestAdminService.php](app/Services/ChangeRequest/ChangeRequestAdminService.php#L12-L31)
	- Bukti dokumentasi: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L3070-L3109)
- **Detail Change Request** di dokumentasi_api hanya memuat `id`, `fitur`, `status`, `details`, sedangkan implementasi menambah `by_user`, `note`, `created_at`, `updated_at`.
	- Bukti implementasi: [app/Services/ChangeRequest/ChangeRequestAdminService.php](app/Services/ChangeRequest/ChangeRequestAdminService.php#L33-L77)
	- Bukti dokumentasi: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L3117-L3148)
- **Accept/Reject Change Request** di dokumentasi_api mengembalikan `{ id, status }`, sedangkan implementasi mengembalikan struktur lengkap seperti `detail`.
	- Bukti implementasi: [app/Http/Controllers/Api/ChangeRequestAdminController.php](app/Http/Controllers/Api/ChangeRequestAdminController.php#L38-L92)
	- Bukti dokumentasi: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L3162-L3202)
- **Dampak**: konsumen API yang mengikuti dokumentasi akan salah mem-parsing field response.

### 12) Diklat HRD response berbeda dari dokumentasi (field id/nama)

- **Endpoint** `/api/diklat/all`.
- **Dokumentasi API** menggunakan field `id` untuk item list.
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L748-L774)
- **Implementasi** menggunakan `id_diklat`.
	- Bukti: [app/Services/Diklat/HrdService.php](app/Services/Diklat/HrdService.php#L63-L87)
- **Dampak**: client yang mengikuti dokumentasi akan gagal membaca id item.

### 13) Diklat HRD response field berbeda (status vs status_diklat)

- **Endpoint** `/api/hrd/diklat/status/layak` dan `/api/hrd/diklat/status/validasi`.
- **Dokumentasi API** menampilkan field `status` (mis. `sudah terlaksana`).
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L872-L903)
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L946-L977)
- **Implementasi** mengembalikan `status_diklat` (dari jadwal) serta `status` hanya dihitung untuk konteks lain.
	- Bukti: [app/Services/Diklat/HrdService.php](app/Services/Diklat/HrdService.php#L149-L207)
- **Dampak**: perbedaan nama field status membuat parsing data tidak konsisten.

### 14) Diklat HRD response field berbeda (id_jadwal_diklat)

- **Endpoint** `/api/hrd/diklat/status/layak` dan `/api/hrd/diklat/status/validasi`.
- **Dokumentasi API** memakai `id_jadwal_diklat`.
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L883-L903)
- **Implementasi** juga memakai `id_jadwal_diklat`, namun `id_diklat` dan `pegawai_id` disertakan dengan struktur berbeda (lebih lengkap) dibanding dokumen.
	- Bukti: [app/Services/Diklat/HrdService.php](app/Services/Diklat/HrdService.php#L149-L207)
- **Dampak**: detail payload yang tidak dicantumkan di dokumentasi bisa membingungkan integrator.

### 15) Profile upload error payload tidak konsisten (dok vs implementasi)

- **Dokumentasi API** mencontohkan error validasi KTP berisi dua pesan mime (`pdf` dan `application/pdf`).
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L1370-L1379)
- **Implementasi** memang memakai `mimetypes:application/pdf` dan `mimes:pdf`, sehingga error bisa ganda, tetapi endpoint lain (foto, KK) hanya menampilkan satu type set.
	- Bukti KTP: [app/Http/Requests/Profile/UploadKtpFileRequest.php](app/Http/Requests/Profile/UploadKtpFileRequest.php#L13-L22)
	- Bukti KK: [app/Http/Requests/Profile/UploadKkFileRequest.php](app/Http/Requests/Profile/UploadKkFileRequest.php#L13-L22)
- **Dampak**: format error antar upload tidak konsisten meski fitur sejenis.

### 16) Pegawai list: dokumentasi menyebut HRD/Direktur dummy, implementasi real

- **Dokumentasi API** menyebut role selain admin menerima data dummy.
	- Bukti: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2710-L2716)
- **Implementasi** untuk HRD dan Direktur memakai service yang sama dengan admin (data real).
	- Bukti: [app/Services/Pegawai/PegawaiService.php](app/Services/Pegawai/PegawaiService.php#L11-L22)
	- Bukti: [app/Services/Pegawai/HrdPegawaiService.php](app/Services/Pegawai/HrdPegawaiService.php#L9-L57)
	- Bukti: [app/Services/Pegawai/DirekturPegawaiService.php](app/Services/Pegawai/DirekturPegawaiService.php#L9-L57)
- **Dampak**: dokumentasi tidak mencerminkan data aktual untuk HRD/Direktur.

### 17) Pegawai detail: struktur keluarga tidak konsisten dengan endpoint keluarga

- **Keluarga endpoints** memakai field seperti `nama_lengkap` dan struktur array langsung atau ringkasan.
	- Bukti pasangan: [dokumentasi/dokumentasi_api.md](dokumentasi/dokumentasi_api.md#L2278-L2292)
- **Pegawai detail** mengembalikan keluarga dengan field berbeda (`nama`, `status_hubungan`, `hubungan`).
	- Bukti: [app/Services/Pegawai/AdminPegawaiService.php](app/Services/Pegawai/AdminPegawaiService.php#L82-L131)
- **Dampak**: response embed keluarga di pegawai detail tidak konsisten dengan modul keluarga.

### 18) Pegawai detail: format tanggal berbeda dengan endpoint riwayat karir

- **Riwayat karir endpoints** mengembalikan tanggal dengan format `Y-m-d`.
	- Bukti STR: [app/Services/RiwayatKarir/StrService.php](app/Services/RiwayatKarir/StrService.php#L76-L84)
	- Bukti SIP: [app/Services/RiwayatKarir/SipService.php](app/Services/RiwayatKarir/SipService.php#L78-L86)
- **Pegawai detail** mengembalikan tanggal `started_at`/`ended_at` dan `tanggal_terbit`/`tanggal_kadaluarsa` tanpa format (raw Carbon/DB value).
	- Bukti: [app/Services/Pegawai/AdminPegawaiService.php](app/Services/Pegawai/AdminPegawaiService.php#L135-L176)
- **Dampak**: konsumen API bisa menerima format tanggal berbeda pada data serupa.

## Catatan

- Pada scope yang dicek, belum ditemukan referensi fungsi yang tidak ada di file tempatnya.

