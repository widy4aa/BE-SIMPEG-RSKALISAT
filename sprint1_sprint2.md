# Sprint 1 & Sprint 2: Status Implementasi Fitur Admin, Pegawai, dan HRD

Berdasarkan hasil audit langsung pada *source code* (Controller, Service, dan Route), berikut adalah rekapitulasi status seluruh *job* beserta analisis kelemahan, kekurangan, dan saran perbaikannya.

---

## 1. Job Admin

| Kategori | Fitur | Status | Catatan / Referensi Kode |
| :--- | :--- | :---: | :--- |
| **Profile Admin** | Melihat Data Akun | ✅ | `ProfileController@show` |
| | Mengedit Data Profile Admin | ✅ | `ProfileController@updateProfile` |
| **Manajemen Hak Akses** | Mengubah Role Pegawai | ✅ | `PegawaiController@changeRole` |
| **Dashboard Admin** | Melihat Permintaan Perubahan Data Pegawai | ✅ | `ChangeRequestAdminController@index` |
| | Menyetujui Permintaan Perubahan Data Pegawai | ✅ | `ChangeRequestAdminController@accept` |
| | Menolak Permintaan Perubahan Data Pegawai | ✅ | `ChangeRequestAdminController@reject` |
| **Pegawai** | Melihat Data Pegawai | ✅ | `PegawaiController@index` |
| | Mengubah status Pegawai | ✅ | `PegawaiController@updateInti` |
| | Menambahkan Data Pegawai | ✅ | `PegawaiController@store` |

### 🚨 Kelemahan & Saran (Admin)
*   **Kelemahan (Change Request):** Data pengajuan perubahan disimpan dalam format *payload JSON*. Saat Admin menekan "Setujui", sistem akan me-*replace* data tabel inti dengan *payload JSON* tersebut. Jika di masa depan terjadi perubahan *schema database* (ada kolom yang dihapus/ditambah), fitur *Accept* ini rawan mengalami *error / schema drift*.
*   **Saran:** Tambahkan mekanisme validasi ulang (re-validasi) di dalam `ChangeRequestAdminService->accept()` tepat sebelum data disimpan ke database utama, guna memastikan JSON payload masih relevan.

---

## 2. Job Pegawai (Self Service)

| Kategori | Fitur | Status | Catatan / Referensi Kode |
| :--- | :--- | :---: | :--- |
| **Profile** | Melihat Data Profile | ✅ | `ProfileController` |
| | Mengedit Data Profile | ✅ | `ProfileController` |
| **Data Diklat** | Menambah, Mengubah, Menghapus Laporan | ✅ | `DiklatController` |
| | Melihat Jadwal & Laporan Diklat | ✅ | `DiklatController` |
| | Mencetak CV | ✅ | `CvController` |
| **Riwayat Karir** | Melihat, Menambah, Mengubah, Menghapus Pendidikan | ✅ | `RiwayatKarirController` |
| | Melihat, Menambah, Mengubah, Menghapus Jabatan | ✅ | `RiwayatKarirController` |
| | Melihat, Menambah, Mengubah, Menghapus Pangkat | ✅ | `RiwayatKarirController` |
| | Melihat, Menambah, Mengubah, Menghapus STR | ✅ | `RiwayatKarirController` |
| | Melihat, Menambah, Mengubah, Menghapus SIP | ✅ | `RiwayatKarirController` |
| | Melihat, Menambah, Mengubah, Menghapus Penugasan Klinis | ✅ | `RiwayatKarirController` |
| **Dashboard Pegawai** | (Overview data & riwayat) | ✅ | `DashboardController` |
| **Data Keluarga** | Melihat, Menambah, Menghapus, Mengubah Data | ✅ | `DataKeluargaController` (Pasangan, Anak, Orang Tua, Kontak Darurat, dll) |
| **Notifikasi WA** | Melihat Notifikasi Whatsapp (STR/SIP kedaluwarsa) | ✅ | Terhubung ke Fonnte lewat `WhatsappService` dan `NotificationController` |

### 🚨 Kelemahan & Saran (Pegawai)
*   **Kelemahan (Storage Bloat):** Saat pegawai mengubah riwayat (seperti STR/SIP/Sertifikat) dan mengunggah dokumen PDF baru, atau ketika mereka menghapus riwayat, file PDF lamanya seringkali masih tertinggal di folder server (`storage/app/public/`).
*   **Saran:** Tambahkan implementasi `Storage::delete($oldFilePath)` di setiap fungsi `update` dan `destroy` pada Service untuk memastikan memori server tidak dipenuhi oleh file sampah.

---

## 3. Job HRD

| Kategori | Fitur | Status | Catatan / Referensi Kode |
| :--- | :--- | :---: | :--- |
| **Profile** | Melihat & Mengedit Data Profile | ✅ | `ProfileController` |
| **Data Diklat** | Menambah, Mengubah, Menghapus, Melihat, Cetak CV | ✅ | Menggunakan Controller Shared |
| **Riwayat Karir** | CRUD Pendidikan, Jabatan, Pangkat, STR, SIP, Penugasan Klinis | ✅ | `HrdRiwayatKarirController` (Dirancang terpisah, tidak numpang) |
| **Dashboard HRD** | (Overview Rekapitulasi RS) | ✅ | `HrdDashboardRepository` |
| **Data Keluarga** | Melihat, Menambah, Menghapus, Mengubah | ✅ | `HrdKeluargaController` (Dirancang terpisah) |
| **Diklat (Manajemen)** | Melihat (Nakes & ASN), Status, Mengedit/Menghapus Jadwal | ✅ | `HrdService` |
| | Mencetak Rekap, Drop Down Jenis Diklat | ✅ | `LaporanController` |
| | Verifikasi Validasi Layak/Tidak Layak Laporan Baru | ✅ | Menggunakan sistem *approval* Laporan Diklat |
| **Pegawai** | Melihat Daftar, Detail, & Filter (Status, Jenis, Profesi, dll) | ✅ | Tersedia via `AdminPegawaiRepository` (menggunakan *Query Params*) |
| | Tambah/Ubah Data Pendidikan, Jabatan, Pangkat, STR, SIP, dll | ✅ | Melalui `/api/hrd/pegawai/{id}/*` |
| **STR/SIP** | Melihat Data STR/SIP, Melihat Status (Akan Habis/Aktif) | ✅ | `StrSipController` |

### 🚨 Kelemahan & Saran (HRD)
*   **Kelemahan (STR/SIP Performance):** Endpoint monitoring `getAllStr()` saat ini me-*load* 100% data seluruh STR dari database ke RAM (memori aplikasi) sebelum difilter dengan *looping foreach* di dalam PHP. Ini berpotensi kuat memicu *Out of Memory* atau *Timeout* jika data rumah sakit sudah bertambah puluhan ribu dalam 5 tahun.
*   **Saran (STR/SIP):** Segera ubah logika tersebut untuk membebankan kerja filter ke Database SQL (menggunakan fungsi `WHERE tanggal_kadaluarsa < NOW()`) dan terapkan *Pagination*.
*   **Kelemahan (Filter Pegawai HRD vs Admin):** Untuk menampilkan daftar pegawai, `HrdPegawaiService` dan `DirekturPegawaiService` saat ini masih meminjam logic dari `AdminPegawaiRepository`. Hal ini berbahaya (*Piggybacking*) karena bila ke depan HRD butuh algoritma *filter* yang diubah, dapat merusak fitur Admin.
*   **Saran (Pegawai):** Buat kelas `HrdPegawaiRepository` dan `DirekturPegawaiRepository` terpisah demi penerapan arsitektur yang aman dan independen.
