# Alur Kerja dan Matriks Aksi Manajemen Diklat

Dokumen ini berisi gambaran lengkap mengenai perbedaan status, alur validasi (skenario normal & revisi), serta tabel matriks hak akses atau aksi yang dapat dilakukan oleh Pegawai dan HRD berdasarkan status diklat.

---

## 1. Definisi Status Kelayakan vs Status Validasi

| Indikator | Berlaku Untuk | Value Disetujui | Value Ditolak | Status Awal (Menunggu) |
| :--- | :--- | :--- | :--- | :--- |
| **Kelayakan (`status_kelayakan`)** | Diklat Eksternal | `"layak"` | `"tidak layak"` | `pending` (`null`) |
| **Validasi (`status_validasi`)** | Diklat Internal | `"valid"` *(Teks: "sudah di validasi")* | `"tidak valid"` *(Teks: "Validasi di tolak")* | `pending` (`null`) *(Teks: "Belum upload..." / "udah upload...")* |

*Catatan: Jika diklat bersifat Eksternal, maka pembacaan teks status validasi akan mengembalikan nilai `"None"` / Tidak Berlaku karena menggunakan status kelayakan.*

---

## 2. Gambaran Alur Validasi Diklat Internal (Bentuk Kotak / Text Boxes)

### A. Alur Utama (Normal / Sukses)

```text
┌─────────────────────────────────────────────────────────────────┐
│ 1. PENDAFTARAN PESERTA DIKLAT INTERNAL                          │
├─────────────────────────────────────────────────────────────────┤
│ • Status Kelayakan  : "layak" (Otomatis)                        │
│ • Status Validasi   : pending (null)                            │
│ • Tampilan Frontend : "Belum upload laporan"                    │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. PEGAWAI MENGUNGGAH LAPORAN / SERTIFIKAT                      │
├─────────────────────────────────────────────────────────────────┤
│ • Kondisi           : Diklat telah selesai dilaksanakan         │
│ • Status Validasi   : pending (Menunggu pemeriksaan HRD)        │
│ • Tampilan Frontend : "udah upload laporan namun belum          │
│                       di validasi"                              │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. PEMERIKSAAN & VALIDASI OLEH HRD                              │
├─────────────────────────────────────────────────────────────────┤
│ • Keputusan HRD     : Disetujui (Valid)                         │
│ • Status Validasi   : "valid"                                   │
│ • Tampilan Frontend : "sudah di validasi"                       │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. SELESAI & TERKUNCI                                           │
├─────────────────────────────────────────────────────────────────┤
│ • Laporan diklat sudah sah dan terkunci di sistem               │
│ • Pegawai tidak dapat mengunggah ulang / mengganti file lagi    │
└─────────────────────────────────────────────────────────────────┘
```

### B. Alur Alternatif (Penolakan & Revisi Laporan)

```text
┌─────────────────────────────────────────────────────────────────┐
│ 1. UPLOAD LAPORAN (PENDING VALIDASI)                            │
│ • Status Validasi   : pending (null)                            │
│ • Masuk ke antrean menu HRD: "Menunggu Validasi"                │
└─────────────────────────────────────────────────────────────────┘
                                 │
                                 ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. PEMERIKSAAN OLEH HRD                                         │
└─────────────────────────────────────────────────────────────────┘
                │                                 │
        [Ditolak / Salah]                 [Disetujui / Sah]
                │                                 │
                ▼                                 ▼
┌───────────────────────────────┐ ┌───────────────────────────────┐
│ 3A. VALIDASI DITOLAK          │ │ 3B. VALIDASI BERHASIL         │
├───────────────────────────────┤ ├───────────────────────────────┤
│ • Status : "tidak valid"      │ │ • Status : "valid"            │
│ • Teks   : "Validasi di tolak"│ │ • Teks   : "sudah di validasi"│
└───────────────────────────────┘ └───────────────────────────────┘
                │                                 │
                ▼                                 ▼
┌───────────────────────────────┐ ┌───────────────────────────────┐
│ 4. PEGAWAI UPLOAD ULANG       │ │ SELESAI & TERKUNCI            │
├───────────────────────────────┤ └───────────────────────────────┘
│ • Pegawai dapat info revisi   │
│ • Akses upload tombol dibuka  │
│ • Unggah file laporan baru    │
└───────────────────────────────┘
                │
                ▼
┌───────────────────────────────┐
│ 5. RESET OTOMATIS OLEH SISTEM │
├───────────────────────────────┤
│ • Status direset -> pending   │
│ • Kembali lagi ke kotak No. 1 │
│   (Menunggu pemeriksaan HRD)  │
└───────────────────────────────┘
```

---

## 3. Matriks Hak Akses & Aksi Diklat Berdasarkan Status

### A. Berdasarkan Status Pelaksanaan (Waktu Pelaksanaan)

| Status Pelaksanaan | Aksi Pegawai | Aksi HRD |
| :--- | :--- | :--- |
| **`mendatang`**<br>*(Belum dimulai)* | • Melihat detail jadwal & lokasi diklat.<br>• *Tidak bisa* mengunggah laporan/sertifikat. | • Mengubah/mengedit informasi master diklat.<br>• Menambah atau menghapus daftar peserta terdaftar (`syncPeserta`). |
| **`berlangsung`**<br>*(Sedang berjalan)* | • Mengikuti pelatihan.<br>• *Tidak bisa* mengunggah laporan/sertifikat. | • Memantau jalannya diklat & daftar kehadiran.<br>• Menambah/menghapus peserta jika ada perubahan mendadak. |
| **`selesai`**<br>*(Sudah berakhir)* | • **Bisa mengunggah (`uploadLaporan`)** bukti sertifikat atau laporan hasil diklat. | • Menunggu unggahan laporan dari peserta untuk dilanjutkan ke proses validasi. |

### B. Berdasarkan Status Kelayakan (Diklat Eksternal)

| Status Kelayakan | Kondisi Laporan | Aksi Pegawai | Aksi HRD |
| :--- | :--- | :--- | :--- |
| **`pending`** | Belum Upload | • Mengunggah bukti laporan/sertifikat (`uploadLaporan`).<br>• Masih bisa mengedit atau membatalkan pengajuan. | • *Belum bisa* menilai kelayakan (karena bukti laporan belum ada). |
| **`pending`** | Sudah Upload | • Bisa memperbarui/mengganti file laporan jika merasa ada yang salah unggah. | • **Menilai Kelayakan:** Menyetujui (`layak`) atau Menolak (`tidak layak`) di menu *Menunggu Kelayakan*. |
| **`layak`** | Sudah Upload | • Melihat status kelayakan disetujui.<br>• **Terkunci:** Tidak bisa lagi mengedit atau menghapus data pengajuan diklat ini. | • Data diklat eksternal pegawai sah dan tercatat di riwayat pengembangan karir. |
| **`tidak layak`** | Sudah Upload | • Menerima notifikasi penolakan dari HRD.<br>• Bisa berkoordinasi dengan HRD jika ada kesalahan lampiran. | • Pengajuan dianggap tidak memenuhi syarat rumah sakit. |

### C. Berdasarkan Status Validasi (Diklat Internal)

| Status Validasi | Tampilan Status | Aksi Pegawai | Aksi HRD |
| :--- | :--- | :--- | :--- |
| **`belum upload`** | *"Belum upload laporan"* | • **Mengunggah Laporan:** Mengunggah file sertifikat/laporan diklat setelah acara selesai. | • Meringatkan pegawai/peserta yang belum mengunggah laporan. |
| **`pending`** | *"Pending (Menunggu Validasi)"* | • **Revisi Mandiri:** Mengunggah ulang file laporan apabila sadar keliru sebelum diperiksa HRD. | • **Memeriksa Laporan:** Menekan tombol Validasi (`valid`) atau Tolak (`tidak valid`) di menu *Menunggu Validasi*. |
| **`valid`** | *"sudah di validasi"* | • **Terkunci Permanen:** Laporan sudah sah. Pegawai *tidak bisa lagi* mengunggah ulang, mengubah, atau menghapus laporan. | • Laporan diklat internal selesai diverifikasi dan masuk rekapitulasi angka kredit/JP. |
| **`tidak valid`** | *"Validasi di tolak"* | • **Upload Ulang (Revisi):** Mengunggah file laporan perbaikan sesuai catatan HRD.<br>• *(Saat diupload ulang, status otomatis kembali menjadi **`pending`**)*. | • Menunggu pegawai mengirimkan laporan perbaikan yang benar. |
