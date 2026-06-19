# Gap Tugas — Rencana Implementasi Fitur Belum Selesai

> **Dibuat:** 19 Juni 2026
> **Scope:** Backend REST API (Laravel)
> **Referensi:** `DokumenPertanggungJawabanBeckend.md`

---

## Ringkasan Gap

| # | Fitur | Prioritas | Estimasi |
|---|-------|-----------|---------|
| 1 | HRD: CRUD Riwayat Pendidikan Pegawai | 🔴 Tinggi | ~2 jam |
| 2 | Pegawai: Tanggungan Lain Self-Service | 🟡 Sedang | ~1.5 jam |
| 3 | Logout Endpoint | 🟡 Sedang | ~30 menit |
| 4 | Ganti Password (saat login) | 🟡 Sedang | ~45 menit |
| 5 | Auto-notif WA saat Status Diklat Berubah | 🟠 Rendah | ~45 menit |

---

## Gap 1 — HRD: CRUD Riwayat Pendidikan Pegawai

**Route yang belum ada:** `GET|POST|PATCH|DELETE /api/hrd/pegawai/{id}/riwayat-karir/pendidikan`

### ⚠️ Catatan Penting

Model `Pendidikan` menggunakan `pegawai_pribadi_id` (FK ke `pegawai_pribadi`), **bukan** `pegawai_id` langsung. Ini berbeda dengan STR/SIP/Penugasan/Pangkat yang langsung pakai `pegawai_id`. Artinya, HRD harus resolve `pegawai_id → pribadi_id` terlebih dahulu sebelum query Pendidikan.

### File yang Dimodifikasi / Dibuat

#### 1. `app/Repositories/Hrd/HrdRiwayatKarirRepository.php` — **Modifikasi**

Tambahkan `use App\Models\Pendidikan;` di bagian import, lalu tambahkan section baru di akhir class:

```php
// ── Pendidikan ────────────────────────────────────────────────────────────────

public function findPribadiByPegawaiId(int $pegawaiId): ?PegawaiPribadi
{
    return \App\Models\PegawaiPribadi::query()->where('pegawai_id', $pegawaiId)->first();
}

public function getPendidikanByPribadiId(int $pribadiId): Collection
{
    return Pendidikan::query()
        ->where('pegawai_pribadi_id', $pribadiId)
        ->orderByDesc('tahun_lulus')
        ->get();
}

public function findPendidikanByIdAndPribadiId(int $id, int $pribadiId): ?Pendidikan
{
    return Pendidikan::query()
        ->where('id', $id)
        ->where('pegawai_pribadi_id', $pribadiId)
        ->first();
}

public function createPendidikan(int $pribadiId, array $data): Pendidikan
{
    $data['pegawai_pribadi_id'] = $pribadiId;
    return Pendidikan::create($data);
}

public function updatePendidikan(Pendidikan $pendidikan, array $data): Pendidikan
{
    $pendidikan->update($data);
    return $pendidikan->fresh();
}

public function deletePendidikan(Pendidikan $pendidikan): void
{
    $pendidikan->delete();
}
```

#### 2. `app/Services/Hrd/HrdRiwayatKarirService.php` — **Modifikasi**

Tambahkan section Pendidikan di akhir class. Ikuti pola `getStr` / `createStr` dst, tapi dengan langkah resolve `pribadi_id`:

```php
// ── Pendidikan ────────────────────────────────────────────────────────────────

public function getPendidikan(int $pegawaiId): array
{
    $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
    if ($pribadi === null) {
        return [];
    }
    return $this->repository->getPendidikanByPribadiId($pribadi->id)->toArray();
}

public function createPendidikan(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
{
    $pegawai = $this->repository->findPegawaiById($pegawaiId);
    if ($pegawai === null) {
        throw new InvalidArgumentException('Data pegawai tidak ditemukan.');
    }

    $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId)
        ?? \App\Models\PegawaiPribadi::create(['pegawai_id' => $pegawaiId]);

    if ($file) {
        $path = $file->store('pendidikan', 'public');
        $payload['ijazah_file_path'] = $path;
    }
    unset($payload['ijazah']); // field upload, bukan kolom DB

    return $this->repository->createPendidikan($pribadi->id, $payload)->toArray();
}

public function updatePendidikan(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
{
    $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
    if ($pribadi === null) {
        throw new ModelNotFoundException('Data pribadi pegawai tidak ditemukan.');
    }

    $pendidikan = $this->repository->findPendidikanByIdAndPribadiId($id, $pribadi->id);
    if ($pendidikan === null) {
        throw new ModelNotFoundException('Data pendidikan tidak ditemukan.');
    }

    if ($file) {
        $path = $file->store('pendidikan', 'public');
        $payload['ijazah_file_path'] = $path;
    }
    unset($payload['ijazah']);

    return $this->repository->updatePendidikan($pendidikan, $payload)->toArray();
}

public function deletePendidikan(int $id, int $pegawaiId): array
{
    $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
    if ($pribadi === null) {
        throw new ModelNotFoundException('Data pribadi pegawai tidak ditemukan.');
    }

    $pendidikan = $this->repository->findPendidikanByIdAndPribadiId($id, $pribadi->id);
    if ($pendidikan === null) {
        throw new ModelNotFoundException('Data pendidikan tidak ditemukan.');
    }

    $this->repository->deletePendidikan($pendidikan);
    return ['id' => $id];
}
```

Pastikan tambahkan import di atas class:
```php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
```

#### 3. `app/Http/Controllers/Api/Hrd/HrdRiwayatKarirController.php` — **Modifikasi**

Tambahkan di bagian import:
```php
use App\Http\Requests\RiwayatKarir\StorePendidikanRequest;
use App\Http\Requests\RiwayatKarir\UpdatePendidikanRequest;
```

Tambahkan section baru sebelum closing `}`:

```php
// ── Pendidikan ────────────────────────────────────────────────────────────────

public function pendidikan(Request $request, int $id): JsonResponse
{
    try {
        return response()->json([
            'success' => true,
            'message' => 'Data riwayat pendidikan berhasil diambil.',
            'data'    => $this->service->getPendidikan($id),
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}

public function storePendidikan(StorePendidikanRequest $request, int $id): JsonResponse
{
    try {
        $result = $this->service->createPendidikan($id, $request->validated(), $request->file('ijazah'));
        return response()->json(['success' => true, 'message' => 'Riwayat pendidikan berhasil ditambahkan.', 'data' => $result], 201);
    } catch (InvalidArgumentException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}

public function updatePendidikan(UpdatePendidikanRequest $request, int $id, int $riwayatId): JsonResponse
{
    try {
        $result = $this->service->updatePendidikan($riwayatId, $id, $request->validated(), $request->file('ijazah'));
        return response()->json(['success' => true, 'message' => 'Riwayat pendidikan berhasil diperbarui.', 'data' => $result]);
    } catch (InvalidArgumentException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    } catch (ModelNotFoundException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}

public function destroyPendidikan(Request $request, int $id, int $riwayatId): JsonResponse
{
    try {
        $result = $this->service->deletePendidikan($riwayatId, $id);
        return response()->json(['success' => true, 'message' => 'Riwayat pendidikan berhasil dihapus.', 'data' => $result]);
    } catch (ModelNotFoundException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}
```

Tambahkan import `ModelNotFoundException` di bagian atas controller jika belum ada.

#### 4. `routes/api.php` — **Modifikasi**

Dalam blok `Route::prefix('hrd/pegawai/{id}')`, tambahkan setelah section pangkat (sebelum reminder):

```php
// Riwayat Karir - pendidikan (HRD)
Route::get('/riwayat-karir/pendidikan', [HrdRiwayatKarirController::class, 'pendidikan']);
Route::post('/riwayat-karir/pendidikan', [HrdRiwayatKarirController::class, 'storePendidikan']);
Route::patch('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePendidikan']);
Route::post('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'updatePendidikan']);
Route::delete('/riwayat-karir/pendidikan/{riwayatId}', [HrdRiwayatKarirController::class, 'destroyPendidikan']);
```

### FormRequests yang Sudah Ada (Tidak Perlu Dibuat)

- ✅ `app/Http/Requests/RiwayatKarir/StorePendidikanRequest.php`
- ✅ `app/Http/Requests/RiwayatKarir/UpdatePendidikanRequest.php`

Field yang divalidasi di `StorePendidikanRequest`:
- `jenjang` required string max:50
- `institusi` required string max:255
- `jurusan` required string max:255
- `tahun_lulus` required integer
- `nomor_ijazah` nullable string max:100
- `ijazah` nullable file mimes:pdf,jpg,jpeg,png,webp max:5120

---

## Gap 2 — Pegawai: Tanggungan Lain Self-Service

**Route yang belum ada:** `GET|POST|PATCH|DELETE /api/keluarga/tanggungan-lain`

### Pola Referensi

Ikuti **persis** pola `KontakDaruratController` + `KontakDaruratService` + `KontakDaruratRepository`.
Model `TanggunganLain` menggunakan `pegawai_pribadi_id`, tidak ada file upload.

### File yang Dibuat

#### 1. `app/Http/Requests/Keluarga/StoreTanggunganLainRequest.php` — **Baru**

```php
<?php
namespace App\Http\Requests\Keluarga;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTanggunganLainRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nama'               => ['required', 'string', 'max:255'],
            'hubungan_keluarga'  => ['required', 'string', 'max:100'],
            'status_tanggungan'  => ['nullable', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
```

#### 2. `app/Http/Requests/Keluarga/UpdateTanggunganLainRequest.php` — **Baru**

Sama dengan Store tapi semua field `sometimes`:
```php
// rules(): semua field pakai ['sometimes', 'required', ...] atau ['sometimes', 'nullable', ...]
'nama'              => ['sometimes', 'required', 'string', 'max:255'],
'hubungan_keluarga' => ['sometimes', 'required', 'string', 'max:100'],
'status_tanggungan' => ['sometimes', 'nullable', 'boolean'],
```

#### 3. `app/Repositories/DataKeluarga/TanggunganLainRepository.php` — **Baru**

Ikuti pola `KontakDaruratRepository`. Methods yang dibutuhkan:
```php
public function getByUserId(int $userId): Collection          // join via user_id → pegawai → pribadi
public function findByIdAndUserId(int $id, int $userId): ?TanggunganLain
public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
public function create(array $data): TanggunganLain
public function update(TanggunganLain $item, array $data): void
public function delete(TanggunganLain $item): void
```

Query `getByUserId`:
```php
return TanggunganLain::query()
    ->whereHas('pegawaiPribadi.pegawai', fn($q) => $q->where('user_id', $userId))
    ->orderByDesc('id')
    ->get();
```

Query `findByIdAndUserId`:
```php
return TanggunganLain::query()
    ->where('id', $id)
    ->whereHas('pegawaiPribadi.pegawai', fn($q) => $q->where('user_id', $userId))
    ->first();
```

#### 4. `app/Services/DataKeluarga/TanggunganLainService.php` — **Baru**

Ikuti persis `KontakDaruratService`. Methods:
```php
public function getAllByUserId(int $userId): array        // return summary dengan 'welcome', 'summary'
public function createByUserId(int $userId, array $payload): array
public function updateById(int $id, int $userId, array $payload): array
public function deleteById(int $id, int $userId): array
```

Return key untuk `createByUserId` dan `updateById`:
```php
return ['id' => $item->id, 'nama' => $item->nama];
```

#### 5. `app/Http/Controllers/Api/Keluarga/TanggunganLainController.php` — **Baru**

Ikuti persis `KontakDaruratController`. Ganti `KontakDaruratService` → `TanggunganLainService`, sesuaikan nama field dan pesan response:

```php
public function index(Request $request): JsonResponse     // GET - list tanggungan lain
public function store(StoreTanggunganLainRequest $request): JsonResponse  // POST - tambah
public function update(UpdateTanggunganLainRequest $request, int $id): JsonResponse  // PATCH - ubah
public function destroy(Request $request, int $id): JsonResponse  // DELETE - hapus
```

#### 6. `routes/api.php` — **Modifikasi**

Dalam blok `Route::middleware([...':admin,pegawai,hrd,direktur'])`, tambahkan setelah section kontak darurat (sekitar baris 85):

```php
// Keluarga - tanggungan lain (self-service)
Route::get('/keluarga/tanggungan-lain', [TanggunganLainController::class, 'index']);
Route::post('/keluarga/tanggungan-lain', [TanggunganLainController::class, 'store']);
Route::patch('/keluarga/tanggungan-lain/{id}', [TanggunganLainController::class, 'update']);
Route::delete('/keluarga/tanggungan-lain/{id}', [TanggunganLainController::class, 'destroy']);
```

Tambahkan import di bagian atas:
```php
use App\Http\Controllers\Api\Keluarga\TanggunganLainController;
```

---

## Gap 3 — Logout Endpoint

**Route yang belum ada:** `POST /api/logout`

### Catatan

Proyek menggunakan custom `JwtAuthMiddleware`. Perlu cek apakah JWT library yang dipakai mendukung token blacklisting. Jika tidak, logout bisa bersifat "client-side only" (kembalikan 200 OK, client hapus token).

### File yang Dimodifikasi

#### 1. `app/Http/Controllers/Api/AuthController.php` — **Modifikasi**

Tambahkan method `logout()`:

```php
public function logout(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
{
    // Jika pakai tymon/jwt-auth:
    // try {
    //     \JWTAuth::invalidate(\JWTAuth::getToken());
    // } catch (\Exception $e) {
    //     // token sudah kadaluarsa atau invalid, abaikan
    // }

    // Jika custom JWT tanpa blacklist:
    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil. Silakan hapus token di sisi client.',
    ]);
}
```

> **Catatan:** Sesuaikan dengan JWT library yang dipakai. Cek `AuthService` untuk metode invalidasi token jika ada.

#### 2. `routes/api.php` — **Modifikasi**

Tambahkan di dalam blok `middleware([JwtAuthMiddleware::class])` yang sesuai (bisa di grup shared semua role):

```php
Route::post('/logout', [AuthController::class, 'logout']);
```

---

## Gap 4 — Ganti Password (Saat Login)

**Route yang belum ada:** `POST /api/auth/change-password`

### File yang Dibuat / Dimodifikasi

#### 1. `app/Http/Requests/Auth/ChangePasswordRequest.php` — **Baru**

```php
<?php
namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'password_lama'   => ['required', 'string'],
            'password_baru'   => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
```

> `confirmed` → otomatis require field `password_baru_confirmation` di request body.

#### 2. `app/Http/Controllers/Api/AuthController.php` — **Modifikasi**

Tambahkan method `changePassword()`. Pattern menggunakan `_jwt_claims` (sama seperti controller lain):

```php
public function changePassword(
    \App\Http\Requests\Auth\ChangePasswordRequest $request
): \Illuminate\Http\JsonResponse {
    $claims = $request->input('_jwt_claims', []);
    $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

    // Panggil AuthService::changePassword($userId, $oldPassword, $newPassword)
    // atau langsung:
    $user = \App\Models\User::find($userId);
    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password_lama, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
    }

    $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password_baru)]);

    return response()->json(['success' => true, 'message' => 'Password berhasil diubah.']);
}
```

> **Catatan:** Idealnya logika ini dipindah ke `AuthService`. Tapi untuk implementasi cepat, boleh langsung di controller (mengikuti pola `SettingController`).

#### 3. `routes/api.php` — **Modifikasi**

Tambahkan di dalam blok shared semua role (bersama logout):

```php
Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
```

---

## Gap 5 — Auto-notif WA saat Status Diklat Berubah

**Trigger:** Setelah `updateStatusKelayakan()` dan `updateStatusValidasi()` di `DiklatService`

### Logic yang Ditambahkan

Setelah status berhasil diupdate, kirim WA ke pegawai yang diklat-nya diverifikasi:

1. Dapatkan data diklat + `pegawai_id` dari `jadwal_diklat`
2. Resolve `no_telp` pegawai via `Pegawai → PegawaiPribadi → no_telp`
3. Format nomor telepon (`08xxx` → `628xxx`)
4. Kirim WA via `WhatsappService::sendMessage()`

### File yang Dimodifikasi

#### `app/Services/Diklat/DiklatService.php` — **Modifikasi**

Inject `WhatsappService` di constructor:
```php
public function __construct(
    private readonly DiklatRepository $repository,
    private readonly WhatsappService $whatsapp,  // tambahkan
) {}
```

Tambahkan helper method private:
```php
private function sendNotifDiklatToWa(int $pegawaiId, string $namaDiklat, string $statusPesan): void
{
    $pegawai = \App\Models\Pegawai::with('pribadi')->find($pegawaiId);
    $noTelp = $pegawai?->pribadi?->no_telp ?? '';
    if ($noTelp === '') return;

    $target = preg_replace('/\D/', '', $noTelp);
    if (str_starts_with($target, '0')) $target = '62' . substr($target, 1);

    $pesan = "Halo {$pegawai->nama},\n\nStatus diklat *{$namaDiklat}* Anda telah diperbarui:\n{$statusPesan}\n\nSilakan login ke aplikasi SIMPEG untuk informasi lebih lanjut.";

    $this->whatsapp->sendMessage($target, $pesan);
    // Error pengiriman WA diabaikan (tidak throw) agar status diklat tetap tersimpan
}
```

Panggil helper di method yang relevan. Contoh di `updateStatusKelayakan()`:
```php
// Setelah $jadwal->update(['status_kelayakan' => $status]):
$namaDiklat = $jadwal->diklat->nama_diklat ?? 'Diklat';
$pesanStatus = $status === 'layak'
    ? '✅ Dinyatakan *LAYAK* mengikuti diklat.'
    : '❌ Dinyatakan *TIDAK LAYAK* mengikuti diklat.';
$this->sendNotifDiklatToWa($jadwal->pegawai_id, $namaDiklat, $pesanStatus);
```

> **Catatan:** Perlu cek nama kolom/field yang tepat di `JadwalDiklat` model dan relasi `diklat` sebelum implementasi.

---

## Urutan Implementasi yang Disarankan

```
1. Gap 3 (Logout)          — paling sederhana, 1 method + 1 route
2. Gap 4 (Ganti Password)  — 1 FormRequest + 1 method + 1 route
3. Gap 1 (Pendidikan HRD)  — modif 3 file existing + 5 route baru
4. Gap 2 (Tanggungan Lain) — 5 file baru + 4 route baru
5. Gap 5 (Auto-notif WA)   — paling bergantung pada struktur DiklatService
```

## Update Dokumentasi Setelah Implementasi

Setiap gap yang selesai diimplementasi, update:
- [ ] `dokumentasi/dokumentasi_api.md` — tambah section endpoint baru, update route count (177 → +N)
- [ ] `dokumentasi/postman/BE-SIMPEG-RSKALISAT.postman_collection.json` — tambah request baru di folder yang relevan
- [ ] `dokumentasi/DokumenPertanggungJawabanBeckend.md` — update status dari ❌ ke ✅
