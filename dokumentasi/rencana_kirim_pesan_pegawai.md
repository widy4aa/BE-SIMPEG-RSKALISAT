# Rencana Implementasi: Fitur Kirim Pesan ke Pegawai (WhatsApp)

## 1. Spesifikasi Endpoint

Karena fitur ini dapat diakses oleh tiga role sekaligus (Admin, HRD, dan Direktur), kita akan membuat satu endpoint *shared* yang dibungkus oleh *middleware* role gabungan.

*   **URL:** `POST /api/pesan/pegawai/{id}`
*   **Method:** `POST`
*   **Middleware:** `auth:api`, `role:admin,hrd,direktur`
*   **Deskripsi:** Mengirim pesan WhatsApp teks langsung ke nomor HP pegawai berdasarkan ID Pegawai.

### A. Request Body
Karena ini dibatasi seperti chat WA pada umumnya (biasanya limit WhatsApp API berkisar di 1024 - 4000 karakter), kita batasi inputnya menjadi maksimal **2000 karakter**.

```json
{
  "pesan": "Tolong segera lengkapi dokumen STR Anda yang akan habis masa berlakunya bulan depan. Terima kasih."
}
```

### B. Response
Kita perlu memetakan (mapping) respons dari `WhatsappService` (Fonnte) ke dalam bahasa Indonesia yang ramah pengguna.

**1. Berhasil:**
```json
{
  "success": true,
  "message": "Pesan berhasil dikirim ke nomor 08123xxxx"
}
```

**2. Gagal (Nomor Tidak Ada):**
```json
{
  "success": false,
  "message": "Pegawai belum memasukkan nomor HP/Telepon."
}
```

**3. Gagal (Kuota Habis):**
```json
{
  "success": false,
  "message": "Gagal mengirim pesan: Kuota WhatsApp (Fonnte) habis."
}
```

**4. Gagal (Token Tidak Valid):**
```json
{
  "success": false,
  "message": "Gagal mengirim pesan: Token WhatsApp tidak valid atau belum disetting."
}
```

---

## 2. Rencana Perubahan Kode (Code Changes)

### Langkah 1: Buat Form Request Validation
Buat `SendPesanPegawaiRequest` untuk memvalidasi input:
```php
public function rules()
{
    return [
        'pesan' => ['required', 'string', 'max:2000'],
    ];
}
```

### Langkah 2: Tambahkan Controller Baru (`MessageController.php`)
Buat sebuah controller baru di `app/Http/Controllers/Api/MessageController.php`. Controller ini akan bertugas:
1. Menerima request.
2. Mencari data pegawai beserta `pribadi->no_hp`.
3. Memanggil `WhatsappService`.
4. Mengembalikan respons.

### Langkah 3: Update `WhatsappService.php` (Error Mapping)
Saat ini `WhatsappService` hanya mengembalikan array mentah dari Exception atau JSON Fonnte. Kita akan menambahkan sedikit logika pembacaan error di `MessageController` (atau di Service-nya) untuk mendeteksi pesan Fonnte, misalnya mencari kata `"Invalid Token"` atau `"Quota"`.

*Contoh Pseudo-code Mapping:*
```php
$reason = strtolower($responseFonnte['reason'] ?? '');
if (str_contains($reason, 'token')) {
    return "Token tidak valid";
} elseif (str_contains($reason, 'quota') || str_contains($reason, 'limit')) {
    return "Kuota habis";
}
```

### Langkah 4: Daftarkan Route di `api.php`
Tambahkan route di bawah group middleware gabungan:
```php
Route::middleware([
    JwtAuthMiddleware::class,
    RoleMiddleware::class.':admin,hrd,direktur'
])->group(function () {
    Route::post('/pesan/pegawai/{id}', [MessageController::class, 'sendToPegawai']);
});
```

---

## 3. Estimasi Pengerjaan
1. Pembuatan Request, Route, dan Controller (10 menit).
2. Integrasi & Mapping Error Fonnte (15 menit).
3. Pengujian API (5 menit).
Total waktu: ~30 menit.
