# Rekomendasi Integrasi Notifikasi WhatsApp (WA Gateway)

Dalam Sistem Informasi Manajemen Pegawai (SIMPEG) untuk Rumah Sakit, fitur pengingat via WhatsApp sangat krusial mengingat tingginya mobilitas tenaga kesehatan. Berikut adalah area yang direkomendasikan untuk diberi notifikasi WA beserta saran implementasinya.

---

## A. Area Kritis yang Membutuhkan Notifikasi WA

### 1. Masa Berlaku Dokumen Esensial (Prioritas Utama) 🚨
Keterlambatan perpanjangan izin dapat berakibat fatal secara hukum operasional RS.
*   **Pengingat Berkala:** Kirim WA H-60, H-30, dan H-7 sebelum STR atau SIP habis masa berlakunya kepada Pegawai.
*   **Pemberitahuan Kedaluwarsa:** Jika sudah jatuh tempo, kirimkan notifikasi dokumen "Tidak Aktif" kepada Pegawai beserta tembusan (BCC) ke Kepala HRD.

### 2. Status Pengajuan Perubahan Data (Change Request) 📝
Mencegah proses birokrasi yang menggantung.
*   **Trigger ke Admin/HRD:** Saat pegawai mengajukan perubahan profil atau keluarga. *(Contoh: "Terdapat 1 pengajuan perubahan data baru dari Dr. Budi menunggu verifikasi Anda.")*
*   **Trigger ke Pegawai:** Saat pengajuan disetujui (Accept) atau ditolak (Reject) beserta alasan penolakan.

### 3. Manajemen Modul Diklat 🎓
*   **Undangan Jadwal Diklat:** Ketika HRD menambahkan/menugaskan pegawai ke dalam jadwal Diklat tertentu.
*   **Hasil Verifikasi Laporan:** Notifikasi ke pegawai saat laporan diklat yang mereka unggah dinyatakan "Layak" atau "Tidak Layak" oleh verifikator.

### 4. Surat Keputusan (SK) dan Penugasan Baru 📄
*   **Trigger ke Pegawai:** Saat HRD memublikasikan SK Pangkat baru atau Penugasan Klinis di sistem, sistem langsung memberi tahu pegawai bersangkutan agar segera mengecek dokumen tersebut.

### 5. Akun & Keamanan 🔐
*   **Reset Password:** Pengiriman link pemulihan atau kode OTP via WA jauh lebih efektif karena tingkat keterbacaannya (open-rate) lebih tinggi dibandingkan email.

---

## B. Saran Implementasi Teknis (Di Laravel)

Untuk menerapkan fitur di atas secara bersih dan efisien, disarankan untuk membagi eksekusi *source code* menjadi dua pendekatan:

### 1. Pendekatan Otomatis/Terjadwal (*Cron Job*) untuk STR/SIP/Penugasan Klinis
Gunakan **Laravel Task Scheduling** untuk mengecek tanggal secara otomatis setiap pagi. Agar fitur ini bisa dikontrol (dinyalakan/dimatikan) oleh Admin, kita perlu menambahkan pengecekan *toggle* ke tabel `settings`.

**Contoh Command Laravel (`CheckExpiredDocuments`):**
```php
public function handle(WhatsappService $waService)
{
    // Cek apakah fitur cron job WA diaktifkan oleh Admin
    $isCronEnabled = Setting::where('key', 'whatsapp_cron_enabled')->value('value');
    if ($isCronEnabled !== 'true' && $isCronEnabled !== '1') {
        $this->info('WhatsApp Cron Job is currently disabled by Admin.');
        return Command::SUCCESS;
    }

    $batasWaktu = now()->addDays(30);
    
    // 1. Cek STR
    $strHampirHabis = StrPegawai::whereDate('tanggal_kadaluarsa', $batasWaktu->toDateString())->get();
    foreach ($strHampirHabis as $str) {
        $noHp = $str->pegawai->pribadi->no_hp;
        $pesan = "Halo {$str->pegawai->nama}, STR Anda dengan nomor {$str->nomor_str} akan kadaluarsa dalam 30 hari ({$str->tanggal_kadaluarsa}). Harap segera perbarui!";
        if ($noHp) $waService->sendMessage($noHp, $pesan);
    }

    // 2. Cek SIP
    $sipHampirHabis = SipPegawai::whereDate('tanggal_kadaluarsa', $batasWaktu->toDateString())->get();
    foreach ($sipHampirHabis as $sip) {
        $noHp = $sip->pegawai->pribadi->no_hp;
        $pesan = "Halo {$sip->pegawai->nama}, SIP Anda dengan nomor {$sip->nomor_sip} akan kadaluarsa dalam 30 hari. Harap segera berkoordinasi.";
        if ($noHp) $waService->sendMessage($noHp, $pesan);
    }

    // 3. Cek Penugasan Klinis
    $pkHampirHabis = PenugasanKlinis::whereDate('tgl_kadaluarsa', $batasWaktu->toDateString())->get();
    foreach ($pkHampirHabis as $pk) {
        $noHp = $pk->pegawai->pribadi->no_hp;
        $pesan = "Halo {$pk->pegawai->nama}, Penugasan Klinis Anda ({$pk->nomor_surat}) akan habis masa berlakunya dalam 30 hari ({$pk->tgl_kadaluarsa}).";
        if ($noHp) $waService->sendMessage($noHp, $pesan);
    }
}
```

**Daftarkan di `app/Console/Kernel.php`:**
```php
protected function schedule(Schedule $schedule)
{
    // Berjalan otomatis setiap jam 8 pagi
    $schedule->command('documents:check-expired')->dailyAt('08:00');
}
```

### 2. Pendekatan Real-time (*Event Listeners / Observers*)
Untuk notifikasi yang dipicu oleh aksi langsung (seperti menyetujui pengajuan atau verifikasi laporan), gunakan **Event Listener** agar logika notifikasi WA tidak mengotori *Controller*.

**Contoh pada Controller (saat Admin setuju pengajuan):**
```php
public function accept($id) 
{
    $request = ChangeRequest::find($id);
    $request->status = 'Disetujui';
    $request->save();

    // Trigger Event, biarkan Listener yang mengurus pengiriman WA
    event(new ChangeRequestAccepted($request));

    return response()->json(['message' => 'Disetujui']);
}
```

**Pada Listener (`SendChangeRequestNotification`):**
```php
public function handle(ChangeRequestAccepted $event)
{
    $noHp = $event->request->pegawai->pribadi->no_hp;
    $pesan = "Pengajuan perubahan data Anda untuk fitur {$event->request->fitur} telah DISETUJUI oleh Admin.";
    
    app(WhatsappService::class)->sendMessage($noHp, $pesan);
}
```

### 3. Pendekatan Manual (Endpoint *Reminder* Khusus)
Selain berjalan otomatis via *Cron*, Admin atau HRD terkadang perlu menekan tombol "Ingatkan Pegawai" secara paksa (manual) dari Dashboard untuk jenis dokumen tertentu (STR/SIP/Penugasan Klinis).

*   **Buat Endpoint:** `POST /api/hrd/pegawai/{id}/reminder`
*   **Request Body JSON:** `{"tipe_dokumen": "str", "dokumen_id": 1}`
*   **Logika Controller Lengkap (Dengan Urgensi & Lampiran Link):**
```php
public function sendManualReminder(Request $request, int $pegawaiId) 
{
    $request->validate([
        'tipe_dokumen' => 'required|in:str,sip,penugasan_klinis',
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
    } elseif ($tipe === 'penugasan_klinis') {
        $doc = \App\Models\PenugasanKlinis::where('id', $docId)->where('pegawai_id', $pegawaiId)->firstOrFail();
        $namaDokumen = 'Penugasan Klinis (' . $doc->nomor_surat . ')';
        $tanggalKadaluarsa = \Carbon\Carbon::parse($doc->tgl_kadaluarsa);
        $skFilePath = $doc->dokumen_file_path;
    }

    $noHp = $doc->pegawai->pribadi->no_hp;
    if (!$noHp) {
        return response()->json(['success' => false, 'message' => 'Nomor HP pegawai tidak tersedia.'], 400);
    }

    // --- 1. Kalkulasi Urgensi (Custom Chat) ---
    $selisihHari = now()->diffInDays($tanggalKadaluarsa, false); // false agar nilai negatif jika sudah lewat
    
    if ($selisihHari < 0) {
        $urgensi = "🚨 *SANGAT MENDESAK* 🚨\nDokumen {$namaDokumen} Anda *TELAH KEDALUWARSA* sejak " . abs($selisihHari) . " hari yang lalu ({$tanggalKadaluarsa->format('d-m-Y')}).";
    } elseif ($selisihHari <= 30) {
        $urgensi = "⚠️ *PENGINGAT PENTING* ⚠️\nDokumen {$namaDokumen} Anda akan segera kedaluwarsa dalam *{$selisihHari} hari* ({$tanggalKadaluarsa->format('d-m-Y')}).";
    } else {
        $urgensi = "ℹ️ *INFORMASI* ℹ️\nDokumen {$namaDokumen} Anda masih aktif hingga {$tanggalKadaluarsa->format('d-m-Y')}, namun kami mengingatkan Anda untuk mengecek kembali statusnya.";
    }

    // --- 2. Rakit Pesan Utama ---
    $pesan = "Halo {$doc->pegawai->nama},\n\n{$urgensi}\n\nHarap segera berkoordinasi dengan pihak HRD untuk melakukan pembaruan dokumen demi kelancaran operasional RS.";

    // --- 3. Sertakan Link Dokumen Jika Ada ---
    if ($skFilePath) {
        $fileUrl = asset($skFilePath);
        $pesan .= "\n\nAnda dapat meninjau dokumen lama Anda pada tautan berikut:\n{$fileUrl}";
    }

    // --- 4. Kirim Pesan ---
    $response = app(WhatsappService::class)->sendMessage($noHp, $pesan);

    return response()->json([
        'success' => true,
        'message' => "Pesan pengingat {$namaDokumen} berhasil dikirim secara manual."
    ]);
}
```

### Kesimpulan
Dengan memisahkan antara yang sifatnya *terjadwal secara otomatis* (dengan kontrol ON/OFF), *reaksional* (Events), dan *eksekusi paksa/manual* (Endpoint), sistem notifikasi WA akan menjadi sangat fleksibel. HRD tetap memegang kendali penuh untuk menghubungi pegawai tanpa takut bot berjalan liar di luar kehendak.
