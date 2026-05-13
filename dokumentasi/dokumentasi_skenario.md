# SKENARIO TESTING SIMPEG
*Sistem Informasi Manajemen Pegawai — Sprint 1 | Sprint 2 | Sprint 3*

> **Keterangan:** ✅ = Expected sukses | ⚠️ = Expected gagal / validasi error

---

## SPRINT 1

### 1. Pengelolaan Profile

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-P-01 | Lihat Profile | Direktur / HRD / Pegawai | 1. Login SIMPEG → 2. Klik "Profile" → 3. Sistem menampilkan halaman profile | ✅ Data profile tampil lengkap (nama, NIK, NIP, jabatan, golongan, dll) | `GET /profile` |
| TC-P-02 | Lengkapi Profile | Direktur / HRD / Pegawai | 1. Login → 2. Klik "Profile" → 3. Klik "Lengkapi Data Profile" → 4. Isi semua field → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /profile/request` |
| TC-P-03 | Lengkapi Profile (field wajib kosong) | Direktur / HRD / Pegawai | 1. Login → 2. Klik "Profile" → 3. Klik "Lengkapi Data Profile" → 4. Biarkan field wajib kosong → 5. Klik "Ajukan Permintaan" | ⚠️ Validasi error pada field yang wajib diisi | `POST /profile/request` |
| TC-P-04 | Edit Profile | Direktur / HRD / Pegawai | 1. Login → 2. Klik "Profile" → 3. Klik "Edit Profile" → 4. Ubah salah satu field (misal: no_telp) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /profile/request/{id}` |
| TC-P-05 | Edit Profile (format email invalid) | Direktur / HRD / Pegawai | 1. Login → 2. Klik "Profile" → 3. Klik "Edit Profile" → 4. Isi email dengan format salah (misal: `bukanemail`) → 5. Klik "Ajukan Permintaan" | ⚠️ Validasi error: format email tidak valid | `PUT /profile/request/{id}` |

---

### 2. Fitur Diklat (Pegawai)

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-D-01 | Lihat Laporan Diklat | Direktur / HRD / Pegawai | 1. Login → 2. Klik "Data Diklat" → 3. Sistem menampilkan daftar laporan | ✅ Daftar tampil: jenis_diklat, nama_kegiatan, penyelenggara, tanggal, biaya | `GET /diklat` |
| TC-D-02 | Tambah Laporan Diklat | Pegawai | 1. Login → 2. Klik "Data Diklat" → 3. Klik "Tambah Diklat" → 4. Isi semua field → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /diklat/request` |
| TC-D-03 | Tambah Diklat (tanggal_selesai < tanggal_mulai) | Pegawai | 1. Login → 2. Klik "Data Diklat" → 3. Klik "Tambah Diklat" → 4. Isi tanggal_selesai lebih awal dari tanggal_mulai → 5. Klik "Ajukan Permintaan" | ⚠️ Validasi error: tanggal selesai tidak boleh lebih awal dari tanggal mulai | `POST /diklat/request` |
| TC-D-04 | Edit Laporan Diklat | Pegawai | 1. Login → 2. Klik "Data Diklat" → 3. Klik icon "Edit" → 4. Ubah field (misal: catatan/total_biaya) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /diklat/request/{id}` |

---

### 3. Riwayat Karir — Pendidikan

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-RK-01 | Lihat Data Pendidikan | Direktur / HRD / Pegawai | 1. Login → 2. Klik "Riwayat Karir" → 3. Klik "Pendidikan" → 4. Sistem menampilkan daftar pendidikan | ✅ Data tampil: institusi, jenjang, jurusan, tahun_lulus, link_ijazah | `GET /riwayat/pendidikan` |
| TC-RK-02 | Tambah Data Pendidikan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pendidikan → 3. Klik "Tambah" → 4. Isi form lengkap → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /riwayat/pendidikan/request` |
| TC-RK-03 | Edit Data Pendidikan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pendidikan → 3. Klik "Edit" → 4. Ubah field (misal: jurusan) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /riwayat/pendidikan/request/{id}` |
| TC-RK-04 | Hapus Data Pendidikan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pendidikan → 3. Klik "Hapus" → 4. Konfirmasi muncul → 5. Klik "Permintaan Hapus" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `DELETE /riwayat/pendidikan/request/{id}` |
| TC-RK-05 | Hapus Data Pendidikan (Batal) | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pendidikan → 3. Klik "Hapus" → 4. Konfirmasi muncul → 5. Klik "Batal" | ⚠️ Data tidak terhapus, tetap tampil di daftar | — |

---

### 4. Riwayat Karir — Jabatan

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-RK-06 | Lihat Data Jabatan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → 3. Klik "Jabatan" | ✅ Data tampil: nama_jabatan, no_sk, link_sk, TMT_mulai, TMT_selesai, unit_kerja | `GET /riwayat/jabatan` |
| TC-RK-07 | Tambah Data Jabatan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Jabatan → 3. Klik "Tambah" → 4. Isi form jabatan → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /riwayat/jabatan/request` |
| TC-RK-08 | Edit Data Jabatan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Jabatan → 3. Klik "Edit" → 4. Ubah field (misal: unit_kerja) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /riwayat/jabatan/request/{id}` |
| TC-RK-09 | Hapus Data Jabatan | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Jabatan → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Permintaan Hapus" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `DELETE /riwayat/jabatan/request/{id}` |
| TC-RK-10 | Hapus Data Jabatan (Batal) | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Jabatan → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Batal" | ⚠️ Data tidak terhapus | — |

---

### 5. Riwayat Karir — Pangkat

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-RK-11 | Lihat Data Pangkat | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → 3. Klik "Pangkat" | ✅ Data tampil: nama_pangkat, pejabat_penetap, link_sk, TMT_sk | `GET /riwayat/pangkat` |
| TC-RK-12 | Tambah Data Pangkat | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pangkat → 3. Klik "Tambah" → 4. Isi form (nama, pejabat_penetap, TMT_sk, link_sk) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /riwayat/pangkat/request` |
| TC-RK-13 | Edit Data Pangkat | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pangkat → 3. Klik "Edit" → 4. Ubah field → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /riwayat/pangkat/request/{id}` |
| TC-RK-14 | Hapus Data Pangkat | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pangkat → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Permintaan Hapus" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `DELETE /riwayat/pangkat/request/{id}` |
| TC-RK-15 | Hapus Data Pangkat (Batal) | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Pangkat → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Batal" | ⚠️ Data tidak terhapus | — |

---

### 6. Riwayat Karir — STR & SIP

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-SS-01 | Lihat Data STR & SIP | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → 3. Klik "STR & SIP" | ✅ Data tampil: no_str, nomor_sip, jenis_sip, tanggal_terbit, tanggal_habis, status | `GET /riwayat/str-sip` |
| TC-SS-02 | Tambah STR & SIP | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → STR & SIP → 3. Klik "Tambah" → 4. Isi semua field → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /riwayat/str-sip/request` |
| TC-SS-03 | Edit STR & SIP | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → STR & SIP → 3. Klik "Edit" → 4. Ubah field (misal: tanggal_habis_sip) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /riwayat/str-sip/request/{id}` |

---

### 7. Riwayat Karir — Penugasan Klinis

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-PK-01 | Lihat Penugasan Klinis | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → 3. Klik "Penugasan Klinis" | ✅ Data tampil: nama_pegawai, no_surat, tanggal_mulai, tanggal_selesai, dokumen | `GET /riwayat/penugasan-klinis` |
| TC-PK-02 | Tambah Penugasan Klinis | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Penugasan Klinis → 3. Klik "Tambah" → 4. Isi form → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /riwayat/penugasan/request` |
| TC-PK-03 | Edit Penugasan Klinis | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Penugasan Klinis → 3. Klik "Edit" → 4. Ubah field → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /riwayat/penugasan/request/{id}` |
| TC-PK-04 | Hapus Penugasan Klinis | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Penugasan Klinis → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Permintaan Hapus" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `DELETE /riwayat/penugasan/request/{id}` |
| TC-PK-05 | Hapus Penugasan Klinis (Batal) | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Penugasan Klinis → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Batal" | ⚠️ Data tidak terhapus | — |

---

### 8. Fitur Perubahan Data (Admin)

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-PD-01 | Lihat Permintaan Perubahan | Admin | 1. Login Admin → 2. Buka dashboard → 3. Sistem tampilkan daftar permintaan → 4. Klik "Detail" | ✅ Detail tampil: fieldLama, fieldBaru, status, catatan | `GET /perubahan` |
| TC-PD-02 | Setujui Permintaan Perubahan | Admin | 1. Login Admin → 2. Buka daftar permintaan → 3. Klik "Detail" → 4. Klik "Setujui" | ✅ Pesan: "Perubahan berhasil disetujui", status berubah | `PUT /perubahan/{id}/approve` |
| TC-PD-03 | Tolak Permintaan Perubahan | Admin | 1. Login Admin → 2. Buka daftar permintaan → 3. Klik "Detail" → 4. Klik "Tolak" → 5. Isi form catatan → 6. Konfirmasi tolak | ✅ Status permintaan berubah ditolak, catatan tersimpan | `PUT /perubahan/{id}/reject` |

---

### 9. Data Keluarga

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-KG-01 | Lihat Data Keluarga | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → 3. Klik "Keluarga" | ✅ Data tampil: nama, hubungan, jenis_kelamin, nik_keluarga, pekerjaan, tanggal_lahir | `GET /keluarga` |
| TC-KG-02 | Tambah Data Keluarga | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Keluarga → 3. Klik "Tambah" → 4. Isi form → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `POST /keluarga/request` |
| TC-KG-03 | Edit Data Keluarga | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Keluarga → 3. Klik "Edit" → 4. Ubah field (misal: pekerjaan) → 5. Klik "Ajukan Permintaan" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `PUT /keluarga/request/{id}` |
| TC-KG-04 | Hapus Data Keluarga | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Keluarga → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Permintaan Hapus" | ✅ Muncul pesan: "Permintaan berhasil diajukan" | `DELETE /keluarga/request/{id}` |
| TC-KG-05 | Hapus Data Keluarga (Batal) | Direktur / HRD / Pegawai | 1. Login → 2. Riwayat Karir → Keluarga → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Batal" | ⚠️ Penghapusan dibatalkan, data tetap ada | — |

---

### 10. Cetak CV

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-CV-01 | Cetak CV | Direktur / HRD / Pegawai | 1. Login → 2. Masuk Dashboard → 3. Klik "Data Diklat" → 4. Klik "Cetak CV" | ✅ Pesan: "Dokumen berhasil dicetak", file CV tergenerate | `GET /cetak-cv` |

---

## SPRINT 2

### 1. Fitur Informasi Diklat (HRD / Direktur)

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-ID-01 | Lihat Diklat Pegawai ASN | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Jenis Diklat" → 4. Klik "Diklat ASN" | ✅ List tampil: nama_pegawai, nama_diklat, profesi, foto, link_dokumen (filter ASN) | `GET /diklat?jenis=ASN` |
| TC-ID-02 | Lihat Diklat Tenaga Kesehatan | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Jenis Diklat" → 4. Klik "Diklat Tenaga Kesehatan" | ✅ List tampil pegawai Tenaga Kesehatan yang telah diklat | `GET /diklat?jenis=nakes` |
| TC-ID-03 | Lihat Status Diklat Pegawai | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Pilih Jenis Diklat → 4. Cek kolom Status tiap pegawai | ✅ Kolom Status (enum) tampil untuk setiap pegawai | `GET /diklat?jenis=ASN` |
| TC-ID-04 | Lihat Jadwal Diklat | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Sistem menampilkan jadwal diklat | ✅ Jadwal tampil: jenis_diklat, nama_kegiatan, penyelenggara, tanggal, waktu, biaya | `GET /diklat/jadwal` |
| TC-ID-05 | Tambah Jadwal Diklat | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Tambah Jadwal" → 4. Isi form → 5. Klik "Simpan" | ✅ Pesan: "Jadwal Berhasil Disimpan" | `POST /diklat/jadwal` |
| TC-ID-06 | Edit Jadwal Diklat | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik icon Edit → 4. Ubah field (misal: tanggal/biaya) → 5. Klik "Simpan" | ✅ Pesan: "Jadwal terbaru berhasil disimpan" | `PUT /diklat/jadwal/{id}` |
| TC-ID-07 | Hapus Jadwal Diklat | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Hapus" | ✅ Pesan: "Jadwal diklat berhasil dihapus" | `DELETE /diklat/jadwal/{id}` |
| TC-ID-08 | Hapus Jadwal Diklat (Batal) | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Hapus" → 4. Konfirmasi → 5. Klik "Batal" | ⚠️ Pesan: "Jadwal diklat gagal dihapus", data tetap ada | — |
| TC-ID-09 | Cetak Rekap Diklat | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Cetak Rekap" → 4. Input bulan_awal & bulan_akhir → 5. Klik "Cetak" | ✅ Pesan: "Sistem sedang memproses dokumen" | `POST /diklat/rekap/cetak` |
| TC-ID-10 | Tambah Dropdown Jenis Diklat | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Tambah Dropdown" → 4. Isi field jenis_baru → 5. Klik "Simpan" | ✅ Pesan: "Dropdown berhasil ditambahkan" | `POST /diklat/jenis` |
| TC-ID-11 | Validasi Dokumen Diklat (Valid) | HRD / Direktur | 1. Login → 2. Klik "Diklat" → 3. Klik "Dokumen Diklat" → 4. Klik "Validasi Dokumen" → 5. Klik "Valid" | ✅ Pesan: "Dokumen berhasil divalidasi" | `PUT /diklat/dokumen/{id}/validasi` |
| TC-ID-12 | Validasi Dokumen Diklat (Tidak Valid) | HRD / Direktur | 1. Login → 2. Klik "Diklat" → Dokumen Diklat → 3. Klik "Validasi Dokumen" → 4. Klik "Tidak Valid" | ⚠️ Pesan: "Dokumen gagal divalidasi" | `PUT /diklat/dokumen/{id}/validasi` |
| TC-ID-13 | Verifikasi Kelayakan (Layak) | HRD / Direktur | 1. Login → 2. Klik "Diklat" → Dokumen Diklat → 3. Klik "Verifikasi" → 4. Klik "Laporan Layak" | ✅ Pesan: "Laporan berhasil ditambahkan kategori layak" | `PUT /diklat/dokumen/{id}/verifikasi` |
| TC-ID-14 | Verifikasi Kelayakan (Tidak Layak) | HRD / Direktur | 1. Login → 2. Klik "Diklat" → Dokumen Diklat → 3. Klik "Verifikasi" → 4. Klik "Laporan Tidak Layak" | ⚠️ Pesan: "Laporan berhasil ditambahkan kategori tidak layak" | `PUT /diklat/dokumen/{id}/verifikasi` |

---

### 2. Fitur STR & SIP (HRD / Direktur)

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-SS-HRD-01 | Lihat STR & SIP Pegawai | HRD / Direktur | 1. Login → 2. Klik "STR & SIP" → 3. Sistem menampilkan daftar | ✅ Data tampil: no_str, jenis_sip, tanggal_terbit, tanggal_habis, status_str, status_sip | `GET /str-sip` |
| TC-SS-HRD-02 | Lihat STR Akan Habis | HRD / Direktur | 1. Login → 2. Klik "STR & SIP" → 3. Klik "Masa STR/SIP" → 4. Klik "STR Akan Habis" | ✅ Daftar STR yang mendekati tanggal habis tampil | `GET /str-sip/str-akan-habis` |
| TC-SS-HRD-03 | Lihat SIP Akan Habis | HRD / Direktur | 1. Login → 2. Klik "STR & SIP" → 3. Klik "Masa STR/SIP" → 4. Klik "SIP Akan Habis" | ✅ Daftar SIP yang mendekati tanggal habis tampil | `GET /str-sip/sip-akan-habis` |
| TC-SS-HRD-04 | Lihat Status SIP (Aktif / Tidak Aktif) | HRD / Direktur | 1. Login → 2. Klik "STR & SIP" → 3. Klik "Status SIP" → 4. Pilih "SIP Aktif" atau "SIP Tidak Aktif" | ✅ Daftar SIP terfilter sesuai status yang dipilih | `GET /str-sip?status_sip=aktif` |

---

## SPRINT 3

### 1. Data Pegawai (HRD / Direktur)

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-PG-01 | Lihat Daftar Pegawai | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Sistem tampilkan daftar | ✅ Daftar tampil: nama, jabatan, NIK, profesi, status, role | `GET /pegawai` |
| TC-PG-02 | Lihat Detail Pegawai | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Detail" → 4. Cek tab Profile → 5. Klik tab Diklat → 6. Klik tab Riwayat Karir | ✅ Semua tab tampil data lengkap sesuai pegawai yang dipilih | `GET /pegawai/{id}` |
| TC-PG-03 | Filter Pegawai Data Tidak Lengkap | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Status Data" → 4. Klik "Filter Data Tidak Lengkap" | ✅ Hanya tampil pegawai yang datanya belum lengkap | `GET /pegawai?status_data=tidak_lengkap` |
| TC-PG-04 | Filter Pegawai Berdasarkan Jenis | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Jenis Pegawai" → 4. Pilih salah satu jenis (misal: ASN) | ✅ Daftar pegawai terfilter berdasarkan jenis | `GET /pegawai?jenis_pegawai=ASN` |
| TC-PG-05 | Filter Pegawai Berdasarkan Profesi | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Filter Profesi" → 4. Pilih profesi (misal: Dokter) | ✅ Daftar pegawai terfilter berdasarkan profesi | `GET /pegawai?profesi=dokter` |
| TC-PG-06 | Filter Berdasarkan Tingkat Pendidikan | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Filter Tingkat Pendidikan" → 4. Pilih tingkat (misal: S1) | ✅ Daftar pegawai terfilter berdasarkan tingkat pendidikan | `GET /pegawai?pendidikan=S1` |
| TC-PG-07 | Filter Status Pegawai | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Filter Status Pegawai" → 4. Pilih status (misal: Aktif) | ✅ Daftar pegawai terfilter berdasarkan status | `GET /pegawai?status=aktif` |
| TC-PG-08 | Filter Berdasarkan Tahun Masuk | HRD / Direktur | 1. Login → 2. Klik "Pegawai" → 3. Klik "Tahun Masuk" → 4. Input tanggal_awal & tanggal_akhir → 5. Klik "Cari" | ✅ Pegawai dalam rentang tahun tersebut tampil | `GET /pegawai?tahun_awal=2020&tahun_akhir=2022` |

---

### 2. Fitur Akun Admin

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-AK-01 | Lihat Data Akun | Admin | 1. Login Admin → 2. Klik "Akun" → 3. Sistem tampilkan data akun | ✅ Data akun tampil: username & password (masked) | `GET /admin/akun` |
| TC-AK-02 | Edit Data Akun (Normal) | Admin | 1. Login Admin → 2. Klik "Akun" → 3. Klik "Edit Akun" → 4. Isi username, password_lama, password_baru → 5. Klik "Simpan Perubahan" | ✅ Pesan: "Data akun admin berhasil diubah" | `PUT /admin/akun` |
| TC-AK-03 | Edit Akun (Password Lama Salah) | Admin | 1. Login Admin → 2. Klik "Akun" → Edit Akun → 3. Isi password_lama dengan nilai salah → 4. Klik "Simpan Perubahan" | ⚠️ Error: password lama tidak sesuai | `PUT /admin/akun` |

---

### 3. Fitur Kepegawaian (Admin)

| No | Nama Skenario | Aktor | Langkah | Expected Result | Endpoint |
|----|--------------|-------|---------|-----------------|----------|
| TC-KA-01 | Lihat Data Pegawai | Admin | 1. Login Admin → 2. Klik "Pegawai" → 3. Sistem tampilkan daftar | ✅ Daftar tampil: nama, jabatan, NIK, profesi, status, role | `GET /admin/pegawai` |
| TC-KA-02 | Ubah Status Pegawai | Admin | 1. Login Admin → 2. Klik "Pegawai" → 3. Klik icon Edit → 4. Pilih Status Baru (misal: Non-Aktif) → 5. Klik "Simpan" | ✅ Pesan: "Perubahan berhasil diubah", status pegawai berubah | `PUT /admin/pegawai/{id}/status` |
| TC-KA-03 | Ubah Role Pegawai | Admin | 1. Login Admin → 2. Klik "Pegawai" → 3. Klik icon Edit → 4. Pilih Role Baru (misal: HRD) → 5. Klik "Simpan" | ✅ Pesan: "Perubahan berhasil diubah", role pegawai berubah | `PUT /admin/pegawai/{id}/role` |

---

## Ringkasan

| Sprint | Jumlah TC |
|--------|-----------|
| Sprint 1 | 39 TC |
| Sprint 2 | 18 TC |
| Sprint 3 | 14 TC |
| **Total** | **71 TC** |
