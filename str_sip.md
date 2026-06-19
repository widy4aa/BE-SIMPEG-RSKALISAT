# Analisis Fitur STR dan SIP (Role HRD)

Secara keseluruhan, fitur pengolahan data **STR (Surat Tanda Registrasi)** dan **SIP (Surat Izin Praktik)** untuk HRD di sistem terbagi menjadi dua bagian utama: Monitoring (Global) dan Manajemen (Per Pegawai).

---

## 1. Fitur Monitoring STR/SIP Global (Dashboard Rekap)
Fitur ini (diakses via `/api/str-sip` melalui `StrSipController` & `StrSipService`) digunakan oleh HRD untuk memantau status masa berlaku STR dan SIP seluruh tenaga kesehatan (nakes) di Rumah Sakit. Statusnya dibagi otomatis menjadi tiga kategori: "Aktif", "Hampir Habis" (kurang dari 30 hari), dan "Tidak Aktif".

*   **Saran Implementasi:**
    Pemisahan status "Hampir Habis" sangat berguna agar HRD bisa proaktif menegur perawat/dokter yang izin praktiknya hampir kedaluwarsa tanpa harus mengeceknya satu per satu.
*   **Kelemahan Kritis (Performance Issue):**
    *   **Tidak Ada Pagination & Filtering di Level Database:** Saat ini, kode `getAllStr()` dan `getAllSip()` **menarik 100% seluruh riwayat STR/SIP** dari database ke memori aplikasi. Setelah itu, kode mem-parsingnya satu per satu menggunakan *looping* `foreach` di dalam memori PHP (di `StrSipService.php`) untuk menentukan status masing-masing dokumen.
    *   **Dampak Jangka Panjang:** Jika rumah sakit beroperasi selama lebih dari 10 tahun dan memiliki 500 nakes, akan ada ribuan riwayat STR/SIP di database. Menarik semua data tersebut ke dalam satu *response* JSON tanpa *pagination* akan sangat membebani memori server (RAM) dan menyebabkan halaman merender dengan lambat (*lag/timeout*).
    *   **Solusi:** Sebaiknya perhitungan status dan filtering dilakukan di level SQL (Query Database), contohnya dengan menggunakan *query* `WHERE tanggal_kadaluarsa <= DATE_ADD(NOW(), INTERVAL 30 DAY)`.

---

## 2. Fitur Manajemen/Edit STR & SIP per Pegawai (CRUD)
Fitur ini dikelola oleh `HrdRiwayatKarirController` dan digunakan ketika HRD perlu melakukan manipulasi data (menambah, mengubah riwayat, nomor surat, atau mengunggah ulang file PDF SK STR/SIP) milik seorang pegawai tertentu.

*   **Saran Implementasi:**
    *   Pendekatan pembuatan *controller* khusus (`HrdRiwayatKarirController`) sudah sangat tepat. Hal ini membuat HRD bisa memanipulasi data riwayat pegawai lain secara independen dan tidak *piggybacking* (numpang) ke jalur API *Self-Service* milik pegawai.
*   **Kelemahan (Human Error & Storage):**
    *   **Overlapping Riwayat:** Jika HRD secara tidak sengaja meng-input dua STR yang masa berlakunya bertabrakan/tumpang tindih di periode yang sama, apakah sistem akan memberikan peringatan? Seringkali dibutuhkan validasi tanggal untuk menghindari *human error*.
    *   **Manajemen File PDF (Storage Bloat):** Apabila HRD menghapus riwayat STR/SIP (melalui method `destroyStr`), atau menimpa dokumen lama dengan file PDF yang baru, sangat sering terjadi file PDF yang lama masih tertinggal di direktori `storage/app/public/`. Jika tidak dihapus secara eksplisit menggunakan `Storage::delete()`, maka media penyimpanan server bisa penuh oleh *file sampah*.

---

## Ringkasan Action Plan untuk Fitur STR/SIP
Kelemahan paling mendesak berada di **kinerja (performance) sistem monitoring global**. Sangat direkomendasikan untuk segera me-refaktor `StrSipService` agar mampu menerima *query filter* (misalnya: `?status=hampir_habis`) dan menerapkan fungsi *pagination* pada level kueri database.
