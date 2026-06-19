# Dokumentasi Detail Pegawai & Rencana Pengembangan Edit HRD

Dokumen ini merinci data yang **sebenarnya** ditampilkan pada endpoint `GET /api/pegawai/{id}` berdasarkan audit kode pada Service dan Repository, serta rencana teknis untuk fitur edit oleh HRD.

## 1. Data yang Ditampilkan Saat Ini (Hasil Audit API)

Berdasarkan `AdminPegawaiService` dan `AdminPegawaiRepository`, berikut adalah data riil yang dikirimkan. 
*Catatan: Beberapa field di Service saat ini bernilai `null` karena kolomnya memang tidak ada di database atau belum di-select.*

### A. Pegawai (Inti)
- `id_pegawai`, `nik`, `nip`, `nama`, `email`
- `jabatan`, `unit_kerja`, `profesi`, `jenis_pegawai`
- `golongan_ruang`, `pangkat`, `status_pegawai`
- `tgl_masuk`, `tmt_cpns`, `tmt_pns`, `link_photo_profil`

### B. Pribadi (Hasil Audit Database `pegawai_pribadi`)
Data yang **ada** di database:
- `pendidikan_terakhir`, `no_kk`, `tanggal_lahir`, `jenis_kelamin`, `agama`, `status_perkawinan`, `alamat`, `no_telp`, `email`
- `foto_path`, `ktp_file_path`, `kk_file_path`

Data yang **tidak ada** (di-set `null` di Repository):
- `tempat_lahir` (Tidak ada kolomnya di tabel `pegawai_pribadi`)
- `npwp` (Tidak ada kolomnya di tabel `pegawai_pribadi`)
- `bpjs_kesehatan` (Tidak ada kolomnya di tabel `pegawai_pribadi`)
- `bpjs_ketenagakerjaan` (Tidak ada kolomnya di tabel `pegawai_pribadi`)

### C. Keluarga
- `pasangan`: ID, Nama, Status Hubungan, Pekerjaan.
- `anak`: ID, Nama, Tanggal Lahir, Pendidikan.
- `orang_tua`: ID, Nama Ayah/Ibu, Status Hidup, Alamat.
- `kontak_darurat`: ID, Nama, Hubungan, No HP.
- `tanggungan_lain`: ID, Nama, Hubungan.

### D. Riwayat Karir & Dokumen (Daftar & File)
- `jabatan`: Nama Jabatan, Unit Kerja, Periode, File SK.
- `str`: Nomor STR, Periode, File SK.
- `sip`: Jenis SIP, Nomor, Periode, File SK.
- `penugasan_klinis`: Nomor Surat, Periode, File Dokumen.
- `pangkat`: Nama Pangkat, Pejabat Penetap, Periode.

### E. Diklat
- Daftar pelatihan, kategori, jenis, JP, dan status verifikasi.

---

## 2. Rencana Pengembangan: Edit Data Pegawai oleh HRD

### Strategi Implementasi:

#### 1. Pembersihan Schema & API
- **Action:** Hapus field `bpjs_kesehatan`, `bpjs_ketenagakerjaan`, `npwp`, dan `tempat_lahir` dari return API karena sudah diputuskan tidak diperlukan untuk saat ini.
- **Rencana:** Menghapus sepenuhnya field kosong/null tersebut dari `AdminPegawaiService` dan `AdminPegawaiRepository` agar response lebih bersih dan tidak membingungkan frontend.

#### 2. Endpoint Khusus HRD (Explicit ID)
HRD akan mengedit data menggunakan endpoint baru yang menerima ID pegawai:
- **Pribadi:** `PATCH /api/hrd/pegawai/{id}/pribadi`
- **Riwayat:** `POST /api/hrd/pegawai/{id}/riwayat-{tipe}` (Tipe: jabatan/pendidikan/str/sip)
- **Keluarga Baru:** `POST /api/hrd/pegawai/{id}/keluarga-{tipe}` (Tipe: pasangan/anak/orang_tua/kontak_darurat/tanggungan)
- **Edit/Hapus Keluarga:** `PUT/PATCH /api/hrd/pegawai/keluarga/{id_keluarga}` dan `DELETE /api/hrd/pegawai/keluarga/{id_keluarga}` (termasuk dukungan upload dokumen validasi jika dibutuhkan).

#### 3. Refactor Service Layer
Mengubah `RiwayatKarirService` dan service terkait lainnya agar logic-nya bisa digunakan baik oleh user (self-service) maupun HRD (admin-service) dengan cara memisahkan logic validasi kepemilikan data.

---

## 3. Prioritas Kerja
1.  **Phase 1:** Menghapus secara permanen field `null` (BPJS/NPWP/Tempat Lahir) dari response detail agar API lebih bersih.
2.  **Phase 2:** Membuat route dan controller `HrdPegawaiManagementController` untuk menangani edit data inti (NIK/NIP/Jabatan/Status) serta Data Pribadi.
3.  **Phase 3:** Mengimplementasikan CRUD untuk Perubahan Data Keluarga (Pasangan, Anak, dll) oleh HRD, beserta endpoint pendukungnya.
4.  **Phase 4:** Menambahkan kemampuan HRD untuk mengupload ulang dokumen (SK/Sertifikat milik pegawai maupun dokumen validasi keluarga) jika terjadi kesalahan.
