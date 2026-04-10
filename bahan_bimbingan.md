# Bahan Bimbingan API BE-SIMPEG-RSKALISAT

Dokumen ini disusun untuk pembahasan menyeluruh API backend, dengan fokus yang diminta pada desain database, migration, dan detail auth.

## 1. Ringkasan Sistem

BE-SIMPEG-RSKALISAT adalah backend API HRIS berbasis Laravel 13 dengan pola:

1. Controller -> Service -> Repository
2. Validasi input menggunakan Form Request
3. Auth stateless menggunakan JWT custom
4. Response JSON konsisten dengan field success, message, data

Tujuan arsitektur ini:

1. Memisahkan tanggung jawab per layer agar mudah maintenance
2. Memudahkan pengujian logic bisnis di layer service
3. Menjaga query database tetap terpusat di repository

## 2. Pembahasan Database dan Migration (Poin Utama Bimbingan)

### 2.1 Gambaran Skema

Migration utama ada di:

1. database/migrations/0001_01_01_000000_create_users_table.php
2. database/migrations/2026_04_07_000100_create_hris_tables.php

Secara konseptual, skema dibagi menjadi 4 kelompok:

1. Auth dan akun: users
2. Master referensi: unit_kerja, jenis_pegawai, profesi, jenis_sip, jenis_diklat, kategori_diklat
3. Data inti pegawai: pegawai, pegawai_pribadi, pegawai_pekerjaan, riwayat_pekerjaan, jabatan, pangkat
4. Data pendukung: files, pendidikan, str, sip, penugasan_klinis, keluarga, diklat, list_jadwal_diklat, notification, perubahan_data, log_activity

### 2.2 Tabel Auth: users

Tabel users didesain untuk autentikasi berbasis NIK yang disimpan di kolom username.

Kolom penting:

1. username unique untuk identitas login
2. password untuk hash password
3. role enum: admin, direktur, hrd, pegawai
4. is_active untuk kontrol akses akun

Kenapa penting:

1. Role disimpan langsung di users agar authorisasi middleware cepat
2. is_active memberi kontrol nonaktif akun tanpa hapus data user

### 2.3 Relasi Utama Pegawai

Relasi kritis yang perlu dijelaskan saat bimbingan:

1. users 1:1 pegawai (pegawai.user_id unique)
2. pegawai 1:1 pegawai_pribadi
3. pegawai 1:n pegawai_pekerjaan
4. pegawai_pekerjaan 1:n str/sip/penugasan_klinis
5. pegawai 1:n keluarga
6. pegawai 1:n list_jadwal_diklat

Nilai desainnya:

1. Data identitas akun dipisah dari data SDM
2. Riwayat pekerjaan bisa disimpan temporal (historical)
3. Data legal dokumen (STR/SIP) terhubung dengan files

### 2.4 Strategi Foreign Key (Poin yang biasanya ditanya dosen)

Project ini menggunakan kombinasi 3 strategi FK:

1. cascadeOnDelete: saat parent dihapus, child ikut terhapus
2. nullOnDelete: saat parent dihapus, FK child di-set null
3. restrictOnDelete: parent tidak boleh dihapus jika masih dipakai child

Contoh implementasi:

1. pegawai.user_id -> users pakai cascadeOnDelete
2. pegawai_pekerjaan.jabatan_id -> jabatan pakai nullOnDelete
3. jabatan.unit_kerja_id -> unit_kerja pakai restrictOnDelete

Alasan desain:

1. Data yang sifatnya kepemilikan kuat menggunakan cascade
2. Data referensi yang masih mungkin berubah menggunakan nullOnDelete
3. Data master yang harus terjaga integritas diproteksi dengan restrict

### 2.5 Soft Delete

Banyak tabel menggunakan softDeletes, misalnya:

1. pegawai
2. jabatan
3. pegawai_pribadi
4. pegawai_pekerjaan
5. riwayat_pekerjaan
6. files

Manfaat:

1. Audit trail tetap aman
2. Data bisa direstore
3. Menghindari kehilangan data permanen saat operasional

### 2.6 Constraint dan Validasi Level Database

Constraint yang sudah baik untuk dijelaskan:

1. Unique pada username, nik, nip
2. Enum untuk kolom role, jenis_kelamin, status_perkawinan, status_pegawai, golongan_ruang
3. FK eksplisit untuk menjaga integritas antar tabel

Catatan teknis penting:

1. Kombinasi validasi aplikasi + constraint database mencegah data invalid lolos
2. Constraint DB tetap bekerja meskipun API dilewati (misalnya input langsung SQL)

## 3. Seeder dan Data Awal

Seeder utama:

1. DatabaseSeeder memanggil MasterReferensiSeeder dan PegawaiSeeder
2. MasterReferensiSeeder mengisi data referensi (unit kerja, profesi, jenis pegawai, dll)
3. PegawaiSeeder mengisi akun user + profil pegawai + pekerjaan + riwayat

Kekuatan implementasi:

1. Menggunakan firstOrCreate/updateOrCreate agar aman jika seeder dijalankan ulang
2. Menyediakan akun lintas role untuk uji endpoint role-based access

Akun uji:

1. admin: 3174010101010099 / password
2. hrd: 3174010101010098 / password
3. direktur: 3174010101010003 / password
4. pegawai: 3174010101010001 / password

## 4. Detail Auth dari Awal Sampai Endpoint

### 4.1 Alur Login

Urutan proses saat POST /api/login:

1. Request masuk ke AuthController@login
2. LoginRequest memvalidasi nik dan password
3. AuthService memanggil AuthRepository::findByNik
4. Hash::check memverifikasi password
5. Cek user is_active
6. JwtService::generate membuat token JWT berisi sub, nik, role
7. Controller mengembalikan response token + profil ringkas user

### 4.2 Komponen Auth dan Fungsinya

1. LoginRequest: validasi format input
2. AuthController: orkestrasi request/response
3. AuthService: business logic autentikasi
4. AuthRepository: query user + relasi pegawai
5. JwtService: generate dan verify JWT
6. JwtAuthMiddleware: validasi bearer token untuk endpoint protected
7. RoleMiddleware: membatasi role yang diizinkan

### 4.3 Detail JWT di Project

Konfigurasi:

1. Secret: config/jwt.php -> JWT_SECRET atau fallback APP_KEY
2. TTL default: 43200 detik
3. Algoritma: HS256

Claim default token:

1. iss
2. iat
3. nbf
4. exp

Claim payload bisnis:

1. sub (id user)
2. nik
3. role

Validasi token di verify:

1. Struktur token harus 3 segmen
2. Algoritma harus HS256
3. Signature harus valid
4. Waktu nbf/exp harus valid

### 4.4 Authorisasi Role

Route role check memakai middleware:

1. JwtAuthMiddleware
2. RoleMiddleware dengan allowed roles admin, pegawai, hrd, direktur

Jika gagal auth token -> 401 Access denied.
Jika token valid tapi role tidak sesuai -> 403 Access denied.

## 5. Pembahasan Endpoint yang Aktif

### 5.1 GET /api/health

Tujuan:

1. Mengetes API up atau tidak
2. Cocok untuk monitoring dan smoke test

Output:

1. success true
2. message API is running
3. data.status up

### 5.2 POST /api/login

Input:

1. nik
2. password

Skenario hasil:

1. 200: login sukses, return token
2. 422: validasi request gagal
3. 401: nik/password salah
4. 403: akun nonaktif

### 5.3 GET /api/role

Input:

1. Header Authorization Bearer token

Output:

1. 200: role valid + greeting sesuai role
2. 401: token tidak ada/tidak valid
3. 403: role tidak diizinkan

## 6. Potongan Kode Kunci untuk Dipresentasikan

### 6.1 Route API

```php
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([
	JwtAuthMiddleware::class,
	RoleMiddleware::class.':admin,pegawai,hrd,direktur',
])->get('/role', [RoleController::class, 'show']);

Route::get('/health', function (): JsonResponse {
	return response()->json([
		'success' => true,
		'message' => 'API is running',
		'data' => ['status' => 'up'],
	]);
});
```

### 6.2 Validasi LoginRequest

```php
public function rules(): array
{
	return [
		'nik' => ['required', 'string', 'max:30'],
		'password' => ['required', 'string', 'min:6'],
	];
}
```

### 6.3 AuthService Login Logic

```php
public function login(string $nik, string $password): array
{
	$user = $this->authRepository->findByNik($nik);

	if (! $user || ! Hash::check($password, $user->password)) {
		return [
			'success' => false,
			'message' => 'NIK atau password tidak valid.',
			'status' => 401,
		];
	}

	if (! $user->is_active) {
		return [
			'success' => false,
			'message' => 'Akun tidak aktif. Silakan hubungi admin.',
			'status' => 403,
		];
	}

	$jwt = $this->jwtService->generate([
		'sub' => $user->id,
		'nik' => $user->username,
		'role' => $user->role,
	]);

	return [
		'success' => true,
		'message' => 'Login berhasil.',
		'status' => 200,
		'data' => [
			'token_type' => 'Bearer',
			'access_token' => $jwt['token'],
			'expires_in' => $jwt['expires_in'],
		],
	];
}
```

### 6.4 JWT Middleware

```php
public function handle(Request $request, Closure $next): Response
{
	$authorization = (string) $request->header('Authorization', '');

	if (! str_starts_with($authorization, 'Bearer ')) {
		return $this->accessDenied(401);
	}

	$token = trim(substr($authorization, 7));

	if ($token === '') {
		return $this->accessDenied(401);
	}

	$claims = $this->jwtService->verify($token);

	if ($claims === null) {
		return $this->accessDenied(401);
	}

	$request->merge(['_jwt_claims' => $claims]);

	return $next($request);
}
```

### 6.5 Contoh FK Migration Penting

```php
Schema::create('pegawai', function (Blueprint $table) {
	$table->id();
	$table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
	$table->string('nik')->unique();
	$table->string('nip')->unique()->nullable();
	$table->string('nama');
	$table->timestamps();
	$table->softDeletes();
});
```

```php
Schema::create('jabatan', function (Blueprint $table) {
	$table->id();
	$table->string('nama');
	$table->foreignId('unit_kerja_id')->constrained('unit_kerja')->restrictOnDelete();
	$table->timestamps();
	$table->softDeletes();
});
```

## 7. Narasi Siap Ucap Saat Bimbingan

Contoh narasi singkat:

1. Database kami disusun dengan pendekatan domain HRIS: akun, master referensi, data pegawai inti, dan data pendukung.
2. Integritas data dijaga oleh foreign key dengan strategi cascade, null, dan restrict sesuai karakter relasi.
3. Layer auth memisahkan validasi, business logic, query, token service, dan middleware sehingga mudah diuji.
4. JWT dipakai stateless, claim role dipakai kembali oleh middleware otorisasi.
5. Semua endpoint mengembalikan struktur response JSON konsisten agar mudah dikonsumsi frontend.

## 8. Risiko dan Rencana Perbaikan (Nilai Tambah)

Poin yang bisa diajukan sebagai improvement:

1. Tambah refresh token agar sesi lebih aman untuk jangka panjang
2. Tambah rate limiting khusus endpoint login
3. Tambah audit login failed/success ke log_activity
4. Tambah index untuk kolom yang sering difilter pada tabel besar
5. Tambah test feature untuk skenario auth success/failed/forbidden

## 9. Checklist Demo Teknis

1. Jalankan migrate fresh seed
2. Uji GET /api/health
3. Uji POST /api/login (sukses)
4. Uji POST /api/login (password salah)
5. Gunakan token hasil login ke GET /api/role
6. Tunjukkan relasi users -> pegawai di database
7. Tunjukkan migration FK utama dan alasan desainnya

