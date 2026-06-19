# Rencana Implementasi: Notifikasi WA Manual (STR, SIP, & Penugasan Klinis)

Dokumen ini berisi rancangan implementasi teknis untuk fitur pengiriman *reminder* (pengingat) via WhatsApp secara manual oleh HRD/Admin kepada Pegawai, khusus untuk dokumen izin yang akan atau telah kedaluwarsa (STR, SIP, dan Penugasan Klinis).

---

## 1. Spesifikasi Endpoint

Sesuai kebutuhan pengelompokan fitur, kita menggunakan **dua endpoint terpisah** agar *routing* lebih rapi.

**A. Endpoint Reminder STR & SIP:**
*   **URL:** `POST /api/hrd/pegawai/{id}/reminder/str-sip`
*   **Request Body:**
```json
{
  "tipe_dokumen": "str", // Pilihan: "str" atau "sip"
  "dokumen_id": 12
}
```

**B. Endpoint Reminder Penugasan Klinis:**
*   **URL:** `POST /api/hrd/pegawai/{id}/reminder/penugasan-klinis`
*   **Request Body:**
```json
{
  "dokumen_id": 5
}
```
*(Catatan: Untuk penugasan klinis, `tipe_dokumen` tidak wajib dikirim dari Frontend karena *URL*-nya sudah spesifik, tapi di *Controller* kita bisa menetapkannya secara eksplisit).*

---

## 2. Logika Penentuan Urgensi Pesan (Custom Message)

Sistem akan secara otomatis mendeteksi seberapa darurat dokumen tersebut berdasarkan `tanggal_kadaluarsa` (atau `tgl_kadaluarsa` pada Penugasan Klinis) dibandingkan dengan tanggal hari ini.

*   **Sangat Mendesak (Lewat Batas Waktu):**
    > 🚨 *SANGAT MENDESAK* 🚨
    > Dokumen STR Anda *TELAH KEDALUWARSA* sejak 5 hari yang lalu (14-06-2026).
*   **Peringatan Penting (Sisa ≤ 30 Hari):**
    > ⚠️ *PENGINGAT PENTING* ⚠️
    > Dokumen STR Anda akan segera kedaluwarsa dalam *15 hari* (04-07-2026).
*   **Informasi (Sisa > 30 Hari):**
    > ℹ️ *INFORMASI* ℹ️
    > Dokumen STR Anda masih aktif hingga 01-12-2026, namun kami mengingatkan Anda untuk mengecek kembali statusnya.

Jika dokumen PDF (lama) tersedia di dalam sistem, sistem akan melampirkan *link public* menuju dokumen tersebut di akhir pesan.

---

## 3. Rencana Kode Controller

Karena ada dua *endpoint*, kita bisa membuat dua *method* di dalam `HrdRiwayatKarirController` (atau controller yang sudah ada).

### Method 1: Reminder STR & SIP
```php
public function sendReminderStrSip(Request $request, int $pegawaiId) 
{
    $request->validate([
        'tipe_dokumen' => 'required|in:str,sip',
        'dokumen_id'   => 'required|integer',
    ]);

    $tipe = $request->input('tipe_dokumen');
    $docId = $request->input('dokumen_id');

    $doc = null; $namaDokumen = ''; $tanggalKadaluarsa = null; $skFilePath = null;

    if ($tipe === 'str') {
        $doc = \App\Models\StrPegawai::where('id', $docId)->where('pegawai_id', $pegawaiId)->firstOrFail();
        $namaDokumen = 'STR (' . $doc->nomor_str . ')';
        $tanggalKadaluarsa = \Carbon\Carbon::parse($doc->tanggal_kadaluarsa);
        $skFilePath = $doc->sk_file_path;
    } elseif ($tipe === 'sip') {
        $doc = \App\Models\Sip::where('id', $docId)->where('pegawai_id', $pegawaiId)->firstOrFail();
        $namaDokumen = 'SIP (' . $doc->nomor_sip . ')';
        $tanggalKadaluarsa = \Carbon\Carbon::parse($doc->tanggal_kadaluarsa);
        $skFilePath = $doc->sk_file_path;
    }

    return $this->processWaReminder($doc, $namaDokumen, $tanggalKadaluarsa, $skFilePath);
}
```

### Method 2: Reminder Penugasan Klinis
```php
public function sendReminderPenugasanKlinis(Request $request, int $pegawaiId) 
{
    $request->validate([
        'dokumen_id' => 'required|integer',
    ]);

    $docId = $request->input('dokumen_id');

    $doc = \App\Models\PenugasanKlinis::where('id', $docId)->where('pegawai_id', $pegawaiId)->firstOrFail();
    $namaDokumen = 'Penugasan Klinis (' . $doc->nomor_surat . ')';
    $tanggalKadaluarsa = \Carbon\Carbon::parse($doc->tgl_kadaluarsa);
    $skFilePath = $doc->dokumen_file_path;

    return $this->processWaReminder($doc, $namaDokumen, $tanggalKadaluarsa, $skFilePath);
}
```

### Helper Method: Proses Kirim Pesan (Agar tidak duplikat kode)
```php
private function processWaReminder($doc, string $namaDokumen, $tanggalKadaluarsa, ?string $skFilePath)
{
    $noHp = $doc->pegawai->pribadi->no_hp;
    if (!$noHp) {
        return response()->json(['success' => false, 'message' => 'Nomor HP pegawai tidak tersedia.'], 400);
    }

    $selisihHari = now()->diffInDays($tanggalKadaluarsa, false);
    
    if ($selisihHari < 0) {
        $urgensi = "🚨 *SANGAT MENDESAK* 🚨\nDokumen {$namaDokumen} Anda *TELAH KEDALUWARSA* sejak " . abs($selisihHari) . " hari yang lalu ({$tanggalKadaluarsa->format('d-m-Y')}).";
    } elseif ($selisihHari <= 30) {
        $urgensi = "⚠️ *PENGINGAT PENTING* ⚠️\nDokumen {$namaDokumen} Anda akan segera kedaluwarsa dalam *{$selisihHari} hari* ({$tanggalKadaluarsa->format('d-m-Y')}).";
    } else {
        $urgensi = "ℹ️ *INFORMASI* ℹ️\nDokumen {$namaDokumen} Anda masih aktif hingga {$tanggalKadaluarsa->format('d-m-Y')}, namun kami mengingatkan Anda untuk mengecek kembali statusnya.";
    }

    $pesan = "Halo {$doc->pegawai->nama},\n\n{$urgensi}\n\nHarap segera berkoordinasi dengan pihak HRD untuk melakukan pembaruan dokumen demi kelancaran operasional RS.";

    if ($skFilePath) {
        $fileUrl = asset($skFilePath);
        $pesan .= "\n\nAnda dapat meninjau dokumen lama Anda pada tautan berikut:\n{$fileUrl}";
    }

    $response = app(WhatsappService::class)->sendMessage($noHp, $pesan);

    return response()->json([
        'success' => true,
        'message' => "Pesan pengingat {$namaDokumen} berhasil dikirim secara manual."
    ]);
}
```
