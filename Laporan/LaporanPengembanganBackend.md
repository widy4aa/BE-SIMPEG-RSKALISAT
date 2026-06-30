### 3.2.3. Pengembangan Sistem

#### 3.2.3.1. Backend

Pengembangan backend SIMPEG RSKALISAT dibangun menggunakan framework Laravel dengan
arsitektur REST API berpola Controller–Service–Repository dan autentikasi JWT.
Pengembangan dikelompokkan berdasarkan modul Controller, dengan penggunaan method
sesuai kebutuhan, dan dibagi ke dalam empat sprint. Pengujian dan dokumentasi endpoint
dilakukan menggunakan Postman. Modul dan fitur yang dikembangkan adalah sebagai berikut.

---

## Sprint 1 — Autentikasi, Profile, Diklat Personal, Riwayat Karir, Change Request

### A. Pengembangan Modul C_Auth ( /api/login & /api/auth )

> 📸 **[SCREENSHOT 1]** — Koleksi Postman folder **Authentication**. Tampilkan request: `POST /login`, `POST /logout`, `POST /auth/change-password`, `POST /forgot-password/request-otp`, `POST /forgot-password/reset`.
>
> 📸 **[SCREENSHOT 2]** — Cuplikan kode `AuthController` (VS Code), tampilkan method `login()` dan `logout()`.

Fitur yang dikembangkan :

a. Login ( POST /api/login )
   Digunakan untuk autentikasi user dan menerbitkan token JWT. Tidak membutuhkan autentikasi untuk pengaksesan (terbuka untuk publik).

b. Logout ( POST /api/logout )
   Digunakan untuk mengakhiri sesi. Karena JWT bersifat stateless, token wajib dihapus di sisi client. Hanya user yang sudah login yang bisa mengakses fitur ini.

c. Mengganti password ( POST /api/auth/change-password )
   Digunakan untuk mengubah password saat user sedang login. Hanya user yang sudah login yang bisa mengakses fitur ini.

d. Lupa password — minta OTP ( POST /api/forgot-password/request-otp )
   Digunakan untuk meminta kode OTP untuk reset password. Tidak membutuhkan autentikasi untuk pengaksesan (terbuka untuk publik).

e. Lupa password — reset ( POST /api/forgot-password/reset )
   Digunakan untuk menyetel ulang password menggunakan kode OTP. Tidak membutuhkan autentikasi untuk pengaksesan (terbuka untuk publik).

### B. Pengembangan Modul C_Profile ( /api/profile )

> 📸 **[SCREENSHOT 3]** — Koleksi Postman folder **Profile**. Tampilkan request: `GET /me`, `GET /role`, `GET /profile`, `PATCH /profile`, `GET /generate/cv`.
>
> 📸 **[SCREENSHOT 4]** — Cuplikan kode `ProfileController`, tampilkan method `me()` dan `update()`.

Fitur yang dikembangkan :

a. Melihat identitas user login ( GET /api/me )
   Digunakan untuk menampilkan ringkasan data user dan pegawai berdasarkan klaim JWT. Hanya user yang sudah login yang bisa mengakses fitur ini.

b. Melihat role user login ( GET /api/role )
   Digunakan untuk mengembalikan role user yang sedang login. Hanya user yang sudah login yang bisa mengakses fitur ini.

c. Melihat data profile ( GET /api/profile )
   Digunakan untuk menampilkan data akun milik sendiri. Hanya user yang sudah login yang bisa mengakses fitur ini.

d. Mengubah data profile ( PATCH /api/profile )
   Digunakan untuk memperbarui data profil beserta unggahan foto, KTP, dan KK. Hanya user yang sudah login yang bisa mengakses fitur ini.

e. Mencetak CV ( GET /api/generate/cv )
   Digunakan untuk menghasilkan CV pegawai dalam bentuk berkas. Hanya user yang sudah login yang bisa mengakses fitur ini.

### C. Pengembangan Modul C_Diklat (Self) ( /api/diklat )

> 📸 **[SCREENSHOT 5]** — Koleksi Postman folder **Diklat**. Tampilkan request: `GET /diklat`, `POST /diklat`, `PATCH /diklat/{id}`, `DELETE /diklat/{id}`, `POST /diklat/{id}/upload-laporan`.
>
> 📸 **[SCREENSHOT 6]** — Cuplikan kode `Self\DiklatController`, tampilkan method `store()` dan `uploadLaporan()`.

Fitur yang dikembangkan :

a. Melihat diklat ( GET /api/diklat )
   Digunakan untuk melihat data diklat, dengan logika yang menyesuaikan role pengakses. Semua role yang sudah login dapat mengakses fitur ini.

b. Menambah laporan diklat ( POST /api/diklat )
   Digunakan untuk menambahkan laporan diklat pribadi. Hanya pegawai, HRD, dan direktur yang bisa mengakses fitur ini.

c. Mengubah laporan diklat ( PATCH /api/diklat/{id} )
   Digunakan untuk mengubah satu laporan diklat pribadi berdasarkan id. Hanya pegawai, HRD, dan direktur yang bisa mengakses fitur ini.

d. Menghapus laporan diklat ( DELETE /api/diklat/{id} )
   Digunakan untuk menghapus satu laporan diklat pribadi berdasarkan id. Hanya pegawai, HRD, dan direktur yang bisa mengakses fitur ini.

e. Mengunggah laporan diklat ( POST /api/diklat/{id}/upload-laporan )
   Digunakan untuk mengunggah berkas laporan ke diklat yang sudah ada. Hanya pegawai, HRD, dan direktur yang bisa mengakses fitur ini.

### D. Pengembangan Modul C_RiwayatKarir (Self) ( /api/riwayat-karir )

Modul ini mengelola enam jenis riwayat karir pribadi, yaitu pendidikan, jabatan,
pangkat, STR, SIP, dan penugasan klinis. Setiap jenis memiliki pola endpoint yang
seragam (notasi `{jenis}` mewakili keenam jenis tersebut).

> 📸 **[SCREENSHOT 7]** — Koleksi Postman folder **Riwayat Karir**. Tampilkan request salah satu jenis (mis. `pendidikan`): `GET`, `POST`, `PATCH /{id}`, `DELETE /{id}`.
>
> 📸 **[SCREENSHOT 8]** — Cuplikan kode salah satu controller riwayat karir (mis. `PendidikanController`), tampilkan method `store()` dan `update()`.

Fitur yang dikembangkan :

a. Melihat riwayat ( GET /api/riwayat-karir/{jenis} )
   Digunakan untuk melihat seluruh data riwayat milik sendiri. Hanya user yang sudah login yang bisa mengakses fitur ini.

b. Menambah riwayat ( POST /api/riwayat-karir/{jenis} )
   Digunakan untuk menambahkan satu data riwayat baru. Hanya user yang sudah login yang bisa mengakses fitur ini.

c. Mengubah riwayat ( PATCH /api/riwayat-karir/{jenis}/{id} )
   Digunakan untuk mengubah satu data riwayat berdasarkan id. Hanya user yang sudah login yang bisa mengakses fitur ini.

d. Menghapus riwayat ( DELETE /api/riwayat-karir/{jenis}/{id} )
   Digunakan untuk menghapus satu data riwayat berdasarkan id. Hanya user yang sudah login yang bisa mengakses fitur ini.

### E. Pengembangan Modul C_ChangeRequest ( /api/admin/change-requests )

> 📸 **[SCREENSHOT 9]** — Koleksi Postman folder **Change Request**. Tampilkan request: `GET /admin/change-requests`, `GET /admin/change-requests/{id}`, `PATCH .../accept`, `PATCH .../reject`.
>
> 📸 **[SCREENSHOT 10]** — Cuplikan kode `ChangeRequestAdminController`, tampilkan method `accept()` dan `reject()`.

Fitur yang dikembangkan :

a. Melihat permintaan perubahan ( GET /api/admin/change-requests )
   Digunakan untuk melihat daftar permintaan perubahan data dari pegawai. Hanya admin yang bisa mengakses fitur ini.

b. Melihat detail permintaan ( GET /api/admin/change-requests/{id} )
   Digunakan untuk melihat satu permintaan perubahan berdasarkan id. Hanya admin yang bisa mengakses fitur ini.

c. Menyetujui permintaan ( PATCH /api/admin/change-requests/{id}/accept )
   Digunakan untuk menyetujui permintaan perubahan data. Hanya admin yang bisa mengakses fitur ini.

d. Menolak permintaan ( PATCH /api/admin/change-requests/{id}/reject )
   Digunakan untuk menolak permintaan perubahan data. Hanya admin yang bisa mengakses fitur ini.

---

## Sprint 2 — Data Keluarga, Manajemen Diklat HRD, Master Data

### F. Pengembangan Modul C_Keluarga (Self) ( /api/keluarga )

Modul ini mengelola lima jenis data keluarga pribadi, yaitu pasangan, anak, orang
tua, kontak darurat, dan tanggungan lain (notasi `{jenis}` mewakili kelima jenis
tersebut).

> 📸 **[SCREENSHOT 11]** — Koleksi Postman folder **Keluarga**. Tampilkan request: `GET /keluarga` dan request salah satu jenis (mis. `pasangan`): `GET`, `POST`, `PATCH /{id}`, `DELETE /{id}`.
>
> 📸 **[SCREENSHOT 12]** — Cuplikan kode `DataKeluargaController` (method `index()` gabungan) atau salah satu controller jenis keluarga.

Fitur yang dikembangkan :

a. Melihat semua data keluarga ( GET /api/keluarga )
   Digunakan untuk melihat seluruh data keluarga (gabungan) milik sendiri. Hanya user yang sudah login yang bisa mengakses fitur ini.

b. Melihat data per jenis ( GET /api/keluarga/{jenis} )
   Digunakan untuk melihat data keluarga per jenis. Hanya user yang sudah login yang bisa mengakses fitur ini.

c. Menambah data keluarga ( POST /api/keluarga/{jenis} )
   Digunakan untuk menambahkan satu data keluarga baru. Hanya user yang sudah login yang bisa mengakses fitur ini.

d. Mengubah data keluarga ( PATCH /api/keluarga/{jenis}/{id} )
   Digunakan untuk mengubah satu data keluarga berdasarkan id. Hanya user yang sudah login yang bisa mengakses fitur ini.

e. Menghapus data keluarga ( DELETE /api/keluarga/{jenis}/{id} )
   Digunakan untuk menghapus satu data keluarga berdasarkan id. Hanya user yang sudah login yang bisa mengakses fitur ini.

### G. Pengembangan Modul C_Diklat (Managed) ( /api/hrd/diklat )

> 📸 **[SCREENSHOT 13]** — Koleksi Postman folder **Manajemen Diklat (HRD)**. Tampilkan request: `GET /diklat/all`, `POST /hrd/diklat`, `PUT /hrd/diklat/{id}`, `GET /hrd/diklat/{id}/peserta`, `POST /hrd/diklat/{id}/peserta`, status layak/validasi.
>
> 📸 **[SCREENSHOT 14]** — Cuplikan kode `Managed\DiklatController`, tampilkan method `updateStatusKelayakan()` atau `updateStatusValidasi()` (terlihat pemanggilan notifikasi WA otomatis).

Fitur yang dikembangkan :

a. Melihat semua diklat pegawai ( GET /api/diklat/all )
   Digunakan untuk melihat data diklat seluruh pegawai. Hanya HRD dan direktur yang bisa mengakses fitur ini.

b. Menambah master jadwal diklat ( POST /api/hrd/diklat )
   Digunakan untuk membuat master jadwal diklat baru. Hanya HRD yang bisa mengakses fitur ini.

c. Mengubah master jadwal diklat ( PUT /api/hrd/diklat/{id} )
   Digunakan untuk mengubah master jadwal diklat berdasarkan id. Hanya HRD yang bisa mengakses fitur ini.

d. Melihat peserta diklat ( GET /api/hrd/diklat/{id}/peserta )
   Digunakan untuk melihat daftar peserta sebuah jadwal diklat. Hanya HRD yang bisa mengakses fitur ini.

e. Sinkronisasi peserta diklat ( POST /api/hrd/diklat/{id}/peserta )
   Digunakan untuk menambah atau menghapus peserta sebuah jadwal diklat. Hanya HRD yang bisa mengakses fitur ini.

f. Melihat diklat menunggu kelayakan ( GET /api/hrd/diklat/status/layak )
   Digunakan untuk melihat daftar diklat yang menunggu verifikasi kelayakan. Hanya HRD yang bisa mengakses fitur ini.

g. Melihat diklat menunggu validasi ( GET /api/hrd/diklat/status/validasi )
   Digunakan untuk melihat daftar diklat yang menunggu validasi laporan. Hanya HRD yang bisa mengakses fitur ini.

h. Verifikasi kelayakan ( PATCH /api/hrd/diklat/{id}/status/layak )
   Digunakan untuk menetapkan status layak atau tidak layak. Secara otomatis mengirim notifikasi WhatsApp ke pegawai terkait. Hanya HRD yang bisa mengakses fitur ini.

i. Verifikasi validasi ( PATCH /api/hrd/diklat/{id}/status/validasi )
   Digunakan untuk memvalidasi laporan diklat. Secara otomatis mengirim notifikasi WhatsApp ke pegawai terkait. Hanya HRD yang bisa mengakses fitur ini.

j. Cetak rekap diklat ( GET /api/generate/laporan-diklat )
   Digunakan untuk menghasilkan rekap laporan diklat dalam bentuk berkas. Hanya HRD yang bisa mengakses fitur ini.

### H. Pengembangan Modul C_MasterData ( /api/form )

Modul ini mengelola data master yang menjadi sumber pilihan pada form, yaitu kategori
diklat, tipe diklat, jenis pegawai, unit kerja, jenis biaya, golongan ruang, profesi,
dan jenis SIP (notasi `{master}` mewakili kedelapan data master tersebut).

> 📸 **[SCREENSHOT 15]** — Koleksi Postman folder **Master Data / Form**. Tampilkan request `GET /form/{master}` dan `POST/PATCH/DELETE` salah satu master (mis. `kategori-diklat`).
>
> 📸 **[SCREENSHOT 16]** — Cuplikan kode `MasterDataController`, tampilkan salah satu pasangan method `store...()` dan `update...()`.

Fitur yang dikembangkan :

a. Melihat data master ( GET /api/form/{master} )
   Digunakan untuk melihat data master sebagai sumber pilihan form. Hanya user yang sudah login yang bisa mengakses fitur ini.

b. Menambah data master ( POST /api/form/{master} )
   Digunakan untuk menambahkan satu data master baru. Hanya HRD yang bisa mengakses fitur ini.

c. Mengubah data master ( PATCH /api/form/{master}/{id} )
   Digunakan untuk mengubah satu data master berdasarkan id. Hanya HRD yang bisa mengakses fitur ini.

d. Menghapus data master ( DELETE /api/form/{master}/{id} )
   Digunakan untuk menghapus satu data master berdasarkan id. Hanya HRD yang bisa mengakses fitur ini.

---

## Sprint 3 — Manajemen Admin, Monitoring HRD, Dashboard, STR/SIP

### I. Pengembangan Modul C_Pegawai (Managed) ( /api/pegawai )

> 📸 **[SCREENSHOT 17]** — Koleksi Postman folder **Pegawai**. Tampilkan request: `GET /pegawai`, `GET /pegawai/{id}`, `GET /pegawai/{id}/{bagian}`, `POST /pegawai`, `PATCH /pegawai/{id}/change-role`.
>
> 📸 **[SCREENSHOT 18]** — Cuplikan kode `PegawaiListController`, tampilkan method `index()` (beserta logika filter) atau `showBagian()`.

Fitur yang dikembangkan :

a. Melihat daftar pegawai ( GET /api/pegawai )
   Digunakan untuk melihat daftar pegawai beserta filter (profesi, jenis, kelengkapan data, waktu masuk, dan lainnya). Hanya admin, HRD, dan direktur yang bisa mengakses fitur ini.

b. Melihat detail pegawai ( GET /api/pegawai/{id} )
   Digunakan untuk melihat satu data pegawai berdasarkan id. Hanya admin, HRD, dan direktur yang bisa mengakses fitur ini.

c. Melihat detail pegawai per bagian ( GET /api/pegawai/{id}/{bagian} )
   Digunakan untuk melihat detail pegawai per bagian, yaitu pegawai, keluarga, riwayat-karir, atau diklat. Hanya admin, HRD, dan direktur yang bisa mengakses fitur ini.

d. Menambah pegawai ( POST /api/pegawai )
   Digunakan untuk menambahkan data pegawai baru. Hanya admin yang bisa mengakses fitur ini.

e. Mengubah role pegawai ( PATCH /api/pegawai/{id}/change-role )
   Digunakan untuk mengubah role atau status pegawai. Hanya admin yang bisa mengakses fitur ini.

### J. Pengembangan Modul C_Dashboard ( /api/dashboard )

> 📸 **[SCREENSHOT 19]** — Koleksi Postman folder **Dashboard** (`GET /dashboard`), beserta cuplikan kode `DashboardController` yang memetakan repository per role (Admin/HRD/Direktur).

Fitur yang dikembangkan :

a. Melihat dashboard ( GET /api/dashboard )
   Digunakan untuk menampilkan statistik dashboard sesuai role pengakses (Admin, HRD, atau Direktur) secara real-time dari basis data. Hanya user yang sudah login yang bisa mengakses fitur ini.

### K. Pengembangan Modul C_StrSip ( /api/str-sip )

> 📸 **[SCREENSHOT 20]** — Koleksi Postman folder **STR/SIP** (`GET /str-sip`), beserta cuplikan kode `StrSipController`.

Fitur yang dikembangkan :

a. Monitoring STR/SIP ( GET /api/str-sip )
   Digunakan untuk memantau data STR/SIP yang akan habis maupun yang masih aktif. Hanya admin, HRD, dan direktur yang bisa mengakses fitur ini.

### L. Pengembangan Modul C_Setting ( /api/settings/whatsapp )

> 📸 **[SCREENSHOT 21]** — Koleksi Postman folder **Settings** (`GET /settings/whatsapp`, `PUT /settings/whatsapp`).

Fitur yang dikembangkan :

a. Melihat setting WhatsApp ( GET /api/settings/whatsapp )
   Digunakan untuk melihat konfigurasi token WhatsApp. Hanya admin yang bisa mengakses fitur ini.

b. Mengubah setting WhatsApp ( PUT /api/settings/whatsapp )
   Digunakan untuk memperbarui konfigurasi token WhatsApp. Hanya admin yang bisa mengakses fitur ini.

### M. Pengembangan Modul C_Notification ( /api/notifications )

> 📸 **[SCREENSHOT 22]** — Koleksi Postman folder **Notification**. Tampilkan request: `GET /notifications`, `PATCH /notifications/{id}/read`, `PATCH /notifications/read-all`.

Fitur yang dikembangkan :

a. Melihat notifikasi ( GET /api/notifications )
   Digunakan untuk melihat daftar notifikasi in-app milik sendiri. Hanya user yang sudah login yang bisa mengakses fitur ini.

b. Menandai notifikasi terbaca ( PATCH /api/notifications/{id}/read )
   Digunakan untuk menandai satu notifikasi sebagai terbaca. Hanya user yang sudah login yang bisa mengakses fitur ini.

c. Menandai semua terbaca ( PATCH /api/notifications/read-all )
   Digunakan untuk menandai seluruh notifikasi sebagai terbaca. Hanya user yang sudah login yang bisa mengakses fitur ini.

---

## Sprint 4 — Manajemen Data Pegawai oleh HRD & Reminder WhatsApp

### N. Pengembangan Modul C_HrdPegawai ( /api/hrd/pegawai/{id} )

Modul ini memberi HRD wewenang mengelola data pegawai lain, mencakup data inti, data
pribadi, lima jenis data keluarga, dan enam jenis riwayat karir pegawai.

> 📸 **[SCREENSHOT 23]** — Koleksi Postman folder **Manajemen Pegawai (HRD)**. Tampilkan request: `PATCH /hrd/pegawai/{id}/inti`, `PATCH /hrd/pegawai/{id}/pribadi`, beserta sub-folder keluarga dan riwayat-karir pegawai.
>
> 📸 **[SCREENSHOT 24]** — Cuplikan kode `Managed\PegawaiController`, tampilkan method `updateInti()` dan `updatePribadi()`.

Fitur yang dikembangkan :

a. Mengubah data inti pegawai ( PATCH /api/hrd/pegawai/{id}/inti )
   Digunakan untuk mengubah data inti pegawai, seperti nama, NIK, dan jabatan. Hanya HRD yang bisa mengakses fitur ini.

b. Mengubah data pribadi pegawai ( PATCH /api/hrd/pegawai/{id}/pribadi )
   Digunakan untuk mengubah data pribadi pegawai beserta unggahan foto, KTP, dan KK. Hanya HRD yang bisa mengakses fitur ini.

c. Mengelola keluarga pegawai ( CRUD /api/hrd/pegawai/{id}/keluarga/{jenis} )
   Digunakan untuk mengelola data keluarga pegawai (pasangan, anak, orang tua, kontak darurat, tanggungan lain). Hanya HRD yang bisa mengakses fitur ini.

d. Mengelola riwayat karir pegawai ( CRUD /api/hrd/pegawai/{id}/riwayat-karir/{jenis} )
   Digunakan untuk mengelola riwayat karir pegawai (pendidikan, jabatan, pangkat, STR, SIP, penugasan klinis). Hanya HRD yang bisa mengakses fitur ini.

### O. Pengembangan Modul C_Message & Reminder ( /api/pesan & /api/hrd/pegawai/{id}/reminder )

> 📸 **[SCREENSHOT 25]** — Koleksi Postman folder **WhatsApp / Reminder**. Tampilkan request: `POST /pesan/pegawai/{id}`, `POST /hrd/pegawai/{id}/reminder/str-sip`, `POST /hrd/pegawai/{id}/reminder/penugasan-klinis`, `POST /hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan`.
>
> 📸 **[SCREENSHOT 26]** — Cuplikan kode `HrdReminderController` atau `WhatsappService` (integrasi Fonnte).

Fitur yang dikembangkan :

a. Kirim pesan WA manual ( POST /api/pesan/pegawai/{id} )
   Digunakan untuk mengirim pesan WhatsApp manual ke pegawai. Hanya admin, HRD, dan direktur yang bisa mengakses fitur ini.

b. Reminder WA STR/SIP ( POST /api/hrd/pegawai/{id}/reminder/str-sip )
   Digunakan untuk mengirim reminder WhatsApp dokumen STR/SIP yang akan habis. Hanya HRD yang bisa mengakses fitur ini.

c. Reminder WA penugasan klinis ( POST /api/hrd/pegawai/{id}/reminder/penugasan-klinis )
   Digunakan untuk mengirim reminder WhatsApp dokumen penugasan klinis. Hanya HRD yang bisa mengakses fitur ini.

d. Reminder WA upload laporan diklat ( POST /api/hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan )
   Digunakan untuk mengingatkan peserta agar mengunggah laporan diklat. Hanya HRD yang bisa mengakses fitur ini.

---

## P. Pembuatan Dokumentasi

> 📸 **[SCREENSHOT 27]** — Tampilan utama koleksi Postman SIMPEG RSKALISAT (daftar folder modul + environment/variable).
>
> 📸 **[SCREENSHOT 28]** — Contoh detail satu request Postman (tab Authorization Bearer Token, Body, dan contoh Response).

Selain modul-modul di atas, terdapat pula endpoint **Health Check ( GET /api/health )**
yang bersifat publik untuk memeriksa status API tanpa autentikasi.

Dokumentasi dan pengujian endpoint dikembangkan menggunakan Postman, dan bertujuan untuk
mempermudah pengembang dalam melihat serta menggunakan endpoint yang telah dikembangkan.
Tiap endpoint dikelompokkan berdasarkan modul pengembangannya, dengan penggunaan method
sesuai kebutuhan. Seluruh endpoint API telah berjalan dengan **total 191 route aktif**
(termasuk alias `POST` untuk pembaruan data multipart). Modul dan fitur yang
didokumentasikan adalah sebagai berikut.

---

### Tabel 3.2.3.1.1 Daftar API endpoint yang dapat diakses

> **Catatan:** Beberapa endpoint `PATCH` (penanda *alias POST*) memiliki rute kembar
> ber-method `POST` pada path yang sama untuk mengakomodasi pengiriman data
> `multipart/form-data` dari client. Rute kembar tersebut sudah ikut terhitung dalam
> total 191 route aktif.

| No | Modul | Method | Endpoint | Keterangan |
|----|-------|--------|----------|-----------|
| 1 | Auth | POST | /api/login | Login dan terbitkan token JWT |
| 2 | Auth | POST | /api/logout | Logout (hapus token di client) |
| 3 | Auth | POST | /api/auth/change-password | Ganti password saat login |
| 4 | Auth | PATCH | /api/auth/change-nik | Ubah NIK sekaligus username login (admin) |
| 5 | Auth | POST | /api/forgot-password/request-otp | Minta OTP reset password |
| 6 | Auth | POST | /api/forgot-password/reset | Reset password dengan OTP |
| 7 | Profile | GET | /api/me | Identitas user + pegawai dari JWT |
| 8 | Profile | GET | /api/role | Role user yang sedang login |
| 9 | Profile | GET | /api/profile | Lihat data profil sendiri |
| 10 | Profile | PATCH | /api/profile | Ubah data profil |
| 11 | Profile | POST | /api/profile/profile-picture | Upload foto profil |
| 12 | Profile | POST | /api/profil/profil-picture | Upload foto profil (alias ejaan lama) |
| 13 | Profile | POST | /api/profil/ktp | Upload KTP |
| 14 | Profile | POST | /api/profile/kk | Upload KK |
| 15 | Profile | GET | /api/generate/cv | Cetak CV pegawai |
| 16 | Dashboard | GET | /api/dashboard | Dashboard sesuai role |
| 17 | Diklat | GET | /api/diklat | Lihat diklat (logika per role) |
| 18 | Diklat | POST | /api/diklat | Tambah laporan diklat pribadi |
| 19 | Diklat | PATCH | /api/diklat/{id} | Ubah laporan diklat pribadi |
| 20 | Diklat | DELETE | /api/diklat/{id} | Hapus laporan diklat pribadi |
| 21 | Diklat | POST | /api/diklat/{id}/upload-laporan | Upload berkas laporan diklat |
| 22 | Diklat | GET | /api/diklat/all | Lihat diklat semua pegawai (HRD/Direktur) |
| 23 | Diklat | POST | /api/hrd/diklat | Tambah master jadwal diklat |
| 24 | Diklat | PUT | /api/hrd/diklat/{id} | Ubah master jadwal diklat |
| 25 | Diklat | GET | /api/hrd/diklat/{id}/peserta | Lihat peserta diklat |
| 26 | Diklat | POST | /api/hrd/diklat/{id}/peserta | Sinkronisasi peserta diklat |
| 27 | Diklat | GET | /api/hrd/diklat/status/layak | Diklat menunggu kelayakan |
| 28 | Diklat | GET | /api/hrd/diklat/status/validasi | Diklat menunggu validasi |
| 29 | Diklat | PATCH | /api/hrd/diklat/{id}/status/layak | Verifikasi kelayakan (+notif WA) |
| 30 | Diklat | PATCH | /api/hrd/diklat/{id}/status/validasi | Verifikasi validasi (+notif WA) |
| 31 | Diklat | GET | /api/generate/laporan-diklat | Cetak rekap diklat |
| 32 | Keluarga | GET | /api/keluarga | Lihat semua data keluarga (gabungan) |
| 33 | Keluarga | GET | /api/keluarga/pasangan | Lihat data pasangan |
| 34 | Keluarga | POST | /api/keluarga/pasangan | Tambah data pasangan |
| 35 | Keluarga | PATCH | /api/keluarga/pasangan/{id} | Ubah data pasangan (alias POST) |
| 36 | Keluarga | DELETE | /api/keluarga/pasangan/{id} | Hapus data pasangan |
| 37 | Keluarga | GET | /api/keluarga/anak | Lihat data anak |
| 38 | Keluarga | POST | /api/keluarga/anak | Tambah data anak |
| 39 | Keluarga | PATCH | /api/keluarga/anak/{id} | Ubah data anak (alias POST) |
| 40 | Keluarga | DELETE | /api/keluarga/anak/{id} | Hapus data anak |
| 41 | Keluarga | GET | /api/keluarga/orang-tua | Lihat data orang tua |
| 42 | Keluarga | POST | /api/keluarga/orang-tua | Tambah data orang tua |
| 43 | Keluarga | PATCH | /api/keluarga/orang-tua/{id} | Ubah data orang tua |
| 44 | Keluarga | DELETE | /api/keluarga/orang-tua/{id} | Hapus data orang tua |
| 45 | Keluarga | GET | /api/keluarga/kontak-darurat | Lihat data kontak darurat |
| 46 | Keluarga | POST | /api/keluarga/kontak-darurat | Tambah data kontak darurat |
| 47 | Keluarga | PATCH | /api/keluarga/kontak-darurat/{id} | Ubah data kontak darurat |
| 48 | Keluarga | DELETE | /api/keluarga/kontak-darurat/{id} | Hapus data kontak darurat |
| 49 | Keluarga | GET | /api/keluarga/tanggungan-lain | Lihat data tanggungan lain |
| 50 | Keluarga | POST | /api/keluarga/tanggungan-lain | Tambah data tanggungan lain |
| 51 | Keluarga | PATCH | /api/keluarga/tanggungan-lain/{id} | Ubah data tanggungan lain |
| 52 | Keluarga | DELETE | /api/keluarga/tanggungan-lain/{id} | Hapus data tanggungan lain |
| 53 | Riwayat Karir | GET | /api/riwayat-karir/pendidikan | Lihat riwayat pendidikan |
| 54 | Riwayat Karir | POST | /api/riwayat-karir/pendidikan | Tambah riwayat pendidikan |
| 55 | Riwayat Karir | PATCH | /api/riwayat-karir/pendidikan/{id} | Ubah riwayat pendidikan (alias POST) |
| 56 | Riwayat Karir | DELETE | /api/riwayat-karir/pendidikan/{id} | Hapus riwayat pendidikan |
| 57 | Riwayat Karir | GET | /api/riwayat-karir/jabatan | Lihat riwayat jabatan |
| 58 | Riwayat Karir | POST | /api/riwayat-karir/jabatan | Tambah riwayat jabatan |
| 59 | Riwayat Karir | PATCH | /api/riwayat-karir/jabatan/{id} | Ubah riwayat jabatan (alias POST) |
| 60 | Riwayat Karir | DELETE | /api/riwayat-karir/jabatan/{id} | Hapus riwayat jabatan |
| 61 | Riwayat Karir | GET | /api/riwayat-karir/pangkat | Lihat riwayat pangkat |
| 62 | Riwayat Karir | POST | /api/riwayat-karir/pangkat | Tambah riwayat pangkat |
| 63 | Riwayat Karir | PATCH | /api/riwayat-karir/pangkat/{id} | Ubah riwayat pangkat (alias POST) |
| 64 | Riwayat Karir | DELETE | /api/riwayat-karir/pangkat/{id} | Hapus riwayat pangkat |
| 65 | Riwayat Karir | GET | /api/riwayat-karir/str | Lihat riwayat STR |
| 66 | Riwayat Karir | POST | /api/riwayat-karir/str | Tambah riwayat STR |
| 67 | Riwayat Karir | PATCH | /api/riwayat-karir/str/{id} | Ubah riwayat STR (alias POST) |
| 68 | Riwayat Karir | DELETE | /api/riwayat-karir/str/{id} | Hapus riwayat STR |
| 69 | Riwayat Karir | GET | /api/riwayat-karir/sip | Lihat riwayat SIP |
| 70 | Riwayat Karir | POST | /api/riwayat-karir/sip | Tambah riwayat SIP |
| 71 | Riwayat Karir | PATCH | /api/riwayat-karir/sip/{id} | Ubah riwayat SIP (alias POST) |
| 72 | Riwayat Karir | DELETE | /api/riwayat-karir/sip/{id} | Hapus riwayat SIP |
| 73 | Riwayat Karir | GET | /api/riwayat-karir/penugasan-klinis | Lihat riwayat penugasan klinis |
| 74 | Riwayat Karir | POST | /api/riwayat-karir/penugasan-klinis | Tambah riwayat penugasan klinis |
| 75 | Riwayat Karir | PATCH | /api/riwayat-karir/penugasan-klinis/{id} | Ubah riwayat penugasan klinis (alias POST) |
| 76 | Riwayat Karir | DELETE | /api/riwayat-karir/penugasan-klinis/{id} | Hapus riwayat penugasan klinis |
| 77 | Change Request | GET | /api/admin/change-requests | Lihat daftar permintaan perubahan |
| 78 | Change Request | GET | /api/admin/change-requests/{id} | Lihat detail permintaan perubahan |
| 79 | Change Request | PATCH | /api/admin/change-requests/{id}/accept | Setujui permintaan perubahan |
| 80 | Change Request | PATCH | /api/admin/change-requests/{id}/reject | Tolak permintaan perubahan |
| 81 | Pegawai | GET | /api/pegawai | Daftar pegawai + filter |
| 82 | Pegawai | GET | /api/pegawai/{id} | Detail pegawai |
| 83 | Pegawai | GET | /api/pegawai/{id}/{bagian} | Detail pegawai per bagian |
| 84 | Pegawai | POST | /api/pegawai | Tambah pegawai baru |
| 85 | Pegawai | PATCH | /api/pegawai/{id}/change-role | Ubah role/status pegawai |
| 86 | STR/SIP | GET | /api/str-sip | Monitoring STR/SIP (akan habis & aktif) |
| 87 | Settings | GET | /api/settings/whatsapp | Lihat token WhatsApp |
| 88 | Settings | PUT | /api/settings/whatsapp | Ubah token WhatsApp |
| 89 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/inti | Ubah data inti pegawai |
| 90 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/pribadi | Ubah data pribadi pegawai (alias POST) |
| 91 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/keluarga/pasangan | Lihat pasangan pegawai |
| 92 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/keluarga/pasangan | Tambah pasangan pegawai |
| 93 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId} | Ubah pasangan pegawai (alias POST) |
| 94 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/keluarga/pasangan/{keluargaId} | Hapus pasangan pegawai |
| 95 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/keluarga/anak | Lihat anak pegawai |
| 96 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/keluarga/anak | Tambah anak pegawai |
| 97 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/keluarga/anak/{keluargaId} | Ubah anak pegawai (alias POST) |
| 98 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/keluarga/anak/{keluargaId} | Hapus anak pegawai |
| 99 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/keluarga/orang-tua | Lihat orang tua pegawai |
| 100 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/keluarga/orang-tua | Tambah orang tua pegawai |
| 101 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId} | Ubah orang tua pegawai |
| 102 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/keluarga/orang-tua/{keluargaId} | Hapus orang tua pegawai |
| 103 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/keluarga/kontak-darurat | Lihat kontak darurat pegawai |
| 104 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/keluarga/kontak-darurat | Tambah kontak darurat pegawai |
| 105 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId} | Ubah kontak darurat pegawai |
| 106 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/keluarga/kontak-darurat/{keluargaId} | Hapus kontak darurat pegawai |
| 107 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/keluarga/tanggungan-lain | Lihat tanggungan lain pegawai |
| 108 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/keluarga/tanggungan-lain | Tambah tanggungan lain pegawai |
| 109 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId} | Ubah tanggungan lain pegawai |
| 110 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/keluarga/tanggungan-lain/{keluargaId} | Hapus tanggungan lain pegawai |
| 111 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/riwayat-karir/jabatan | Lihat riwayat jabatan pegawai |
| 112 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/riwayat-karir/jabatan | Tambah riwayat jabatan pegawai |
| 113 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId} | Ubah riwayat jabatan pegawai (alias POST) |
| 114 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/riwayat-karir/jabatan/{riwayatId} | Hapus riwayat jabatan pegawai |
| 115 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/riwayat-karir/str | Lihat riwayat STR pegawai |
| 116 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/riwayat-karir/str | Tambah riwayat STR pegawai |
| 117 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId} | Ubah riwayat STR pegawai (alias POST) |
| 118 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/riwayat-karir/str/{riwayatId} | Hapus riwayat STR pegawai |
| 119 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/riwayat-karir/sip | Lihat riwayat SIP pegawai |
| 120 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/riwayat-karir/sip | Tambah riwayat SIP pegawai |
| 121 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId} | Ubah riwayat SIP pegawai (alias POST) |
| 122 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/riwayat-karir/sip/{riwayatId} | Hapus riwayat SIP pegawai |
| 123 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis | Lihat penugasan klinis pegawai |
| 124 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis | Tambah penugasan klinis pegawai |
| 125 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId} | Ubah penugasan klinis pegawai (alias POST) |
| 126 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/riwayat-karir/penugasan-klinis/{riwayatId} | Hapus penugasan klinis pegawai |
| 127 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/riwayat-karir/pangkat | Lihat riwayat pangkat pegawai |
| 128 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/riwayat-karir/pangkat | Tambah riwayat pangkat pegawai |
| 129 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId} | Ubah riwayat pangkat pegawai (alias POST) |
| 130 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/riwayat-karir/pangkat/{riwayatId} | Hapus riwayat pangkat pegawai |
| 131 | HRD Pegawai | GET | /api/hrd/pegawai/{id}/riwayat-karir/pendidikan | Lihat riwayat pendidikan pegawai |
| 132 | HRD Pegawai | POST | /api/hrd/pegawai/{id}/riwayat-karir/pendidikan | Tambah riwayat pendidikan pegawai |
| 133 | HRD Pegawai | PATCH | /api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId} | Ubah riwayat pendidikan pegawai (alias POST) |
| 134 | HRD Pegawai | DELETE | /api/hrd/pegawai/{id}/riwayat-karir/pendidikan/{riwayatId} | Hapus riwayat pendidikan pegawai |
| 135 | Reminder | POST | /api/hrd/pegawai/{id}/reminder/str-sip | Reminder WA STR/SIP |
| 136 | Reminder | POST | /api/hrd/pegawai/{id}/reminder/penugasan-klinis | Reminder WA penugasan klinis |
| 137 | Reminder | POST | /api/hrd/diklat/{diklatId}/pegawai/{pegawaiId}/reminder-upload-laporan | Reminder WA upload laporan diklat |
| 138 | Message | POST | /api/pesan/pegawai/{id} | Kirim pesan WA manual |
| 139 | Master Data | GET | /api/form/kategori-diklat | Lihat master kategori diklat |
| 140 | Master Data | POST | /api/form/kategori-diklat | Tambah master kategori diklat |
| 141 | Master Data | PATCH | /api/form/kategori-diklat/{id} | Ubah master kategori diklat |
| 142 | Master Data | DELETE | /api/form/kategori-diklat/{id} | Hapus master kategori diklat |
| 143 | Master Data | GET | /api/form/tipe-diklat | Lihat master tipe diklat |
| 144 | Master Data | POST | /api/form/tipe-diklat | Tambah master tipe diklat |
| 145 | Master Data | PATCH | /api/form/tipe-diklat/{id} | Ubah master tipe diklat |
| 146 | Master Data | DELETE | /api/form/tipe-diklat/{id} | Hapus master tipe diklat |
| 147 | Master Data | GET | /api/form/jenis-pegawai | Lihat master jenis pegawai |
| 148 | Master Data | POST | /api/form/jenis-pegawai | Tambah master jenis pegawai |
| 149 | Master Data | PATCH | /api/form/jenis-pegawai/{id} | Ubah master jenis pegawai |
| 150 | Master Data | DELETE | /api/form/jenis-pegawai/{id} | Hapus master jenis pegawai |
| 151 | Master Data | GET | /api/form/unit-kerja | Lihat master unit kerja |
| 152 | Master Data | POST | /api/form/unit-kerja | Tambah master unit kerja |
| 153 | Master Data | PATCH | /api/form/unit-kerja/{id} | Ubah master unit kerja |
| 154 | Master Data | DELETE | /api/form/unit-kerja/{id} | Hapus master unit kerja |
| 155 | Master Data | GET | /api/form/jenis-biaya | Lihat master jenis biaya |
| 156 | Master Data | POST | /api/form/jenis-biaya | Tambah master jenis biaya |
| 157 | Master Data | PATCH | /api/form/jenis-biaya/{id} | Ubah master jenis biaya |
| 158 | Master Data | DELETE | /api/form/jenis-biaya/{id} | Hapus master jenis biaya |
| 159 | Master Data | GET | /api/form/golongan-ruang | Lihat master golongan ruang |
| 160 | Master Data | POST | /api/form/golongan-ruang | Tambah master golongan ruang |
| 161 | Master Data | PATCH | /api/form/golongan-ruang/{id} | Ubah master golongan ruang |
| 162 | Master Data | DELETE | /api/form/golongan-ruang/{id} | Hapus master golongan ruang |
| 163 | Master Data | GET | /api/form/profesi | Lihat master profesi |
| 164 | Master Data | POST | /api/form/profesi | Tambah master profesi |
| 165 | Master Data | PATCH | /api/form/profesi/{id} | Ubah master profesi |
| 166 | Master Data | DELETE | /api/form/profesi/{id} | Hapus master profesi |
| 167 | Master Data | GET | /api/form/jenis-sip | Lihat master jenis SIP |
| 168 | Master Data | POST | /api/form/jenis-sip | Tambah master jenis SIP |
| 169 | Master Data | PATCH | /api/form/jenis-sip/{id} | Ubah master jenis SIP |
| 170 | Master Data | DELETE | /api/form/jenis-sip/{id} | Hapus master jenis SIP |
| 171 | Notification | GET | /api/notifications | Lihat notifikasi in-app |
| 172 | Notification | PATCH | /api/notifications/{id}/read | Tandai satu notifikasi terbaca |
| 173 | Notification | PATCH | /api/notifications/read-all | Tandai semua notifikasi terbaca |
| 174 | Health | GET | /api/health | Health check API (publik) |

> **Catatan jumlah:** Tabel di atas memuat **174 endpoint unik** (kombinasi method + path).
> Di samping itu terdapat **17 rute alias `POST`** yang berbagi path dengan endpoint
> `PATCH` (ditandai *(alias POST)* pada kolom Keterangan) untuk mengakomodasi pengiriman
> data `multipart/form-data` dari client. Dengan demikian, total route aktif adalah
> 174 + 17 = **191 route**, sesuai hasil `php artisan route:list`.

Keseluruhan kode dapat dilihat pada repositori proyek SIMPEG RSKALISAT.
