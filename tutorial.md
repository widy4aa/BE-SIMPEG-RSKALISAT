# Tutorial Bikin Endpoint Baru di BE-SIMPEG-RSKALISAT

Dokumen ini menjelaskan alur lengkap dari awal sampai endpoint siap dipakai:

1. Menentukan route dan method
2. Menentukan siapa saja role yang boleh akses
3. Menentukan data request (query, body, header token)
4. Mengambil data dari database (Repository dan Model)
5. Memproses logika bisnis (Service)
6. Membentuk output response endpoint (Controller)

Panduan ini mengikuti pola project ini:

1. Controller -> Service -> Repository
2. Validasi via Form Request
3. Response JSON konsisten

## 1. Alur File (Siapa Mengerjakan Apa)

1. Route:
	[routes/api.php](routes/api.php)
	Tugasnya menentukan URL, method, middleware, dan controller mana yang dipanggil.

2. Middleware auth dan role:
	[app/Http/Middleware/JwtAuthMiddleware.php](app/Http/Middleware/JwtAuthMiddleware.php)
	[app/Http/Middleware/RoleMiddleware.php](app/Http/Middleware/RoleMiddleware.php)
	Tugasnya cek token dan cek role.

3. Controller:
	[app/Http/Controllers/Api](app/Http/Controllers/Api)
	Tugasnya menerima request, panggil service, lalu return response JSON.

4. Form Request:
	[app/Http/Requests](app/Http/Requests)
	Tugasnya validasi input dari client.

5. Service:
	[app/Services](app/Services)
	Tugasnya business logic utama.

6. Repository:
	[app/Repositories](app/Repositories)
	Tugasnya query ke database.

7. Model:
	[app/Models](app/Models)
	Tugasnya representasi tabel dan relasi Eloquent.

## 2. Alur Data (Request -> Response)

1. Client kirim request ke endpoint API.
2. Route mencocokkan URL dan method.
3. Middleware jalan dulu:
	- Cek token Bearer
	- Verifikasi JWT
	- Cek role user
4. Kalau valid, masuk ke Controller.
5. Controller terima input yang sudah tervalidasi, lalu panggil Service.
6. Service jalankan logika bisnis dan panggil Repository.
7. Repository ambil atau simpan data via Model.
8. Hasil balik ke Service untuk diproses.
9. Service kirim hasil akhir ke Controller.
10. Controller bentuk JSON output sesuai format project.

## 3. Langkah Bikin Endpoint Baru

## 3.1 Tentukan spesifikasi endpoint dulu

Sebelum coding, tentukan ini dulu:

1. URL endpoint, misalnya /api/pegawai.
2. Method, misalnya GET untuk list, POST untuk create.
3. Role yang boleh akses, misalnya admin dan hrd.
4. Input yang dibutuhkan:
	- Header: Authorization Bearer token
	- Query untuk GET (search, page, per_page, filter)
	- Body untuk POST atau PUT
5. Output yang diharapkan.

## 3.2 Tambah route endpoint

Edit file [routes/api.php](routes/api.php).

Contoh pola route endpoint dengan auth dan role:

	 use App\Http\Controllers\Api\PegawaiController;
	 use App\Http\Middleware\JwtAuthMiddleware;
	 use App\Http\Middleware\RoleMiddleware;
	 use Illuminate\Support\Facades\Route;

	 Route::middleware([
		  JwtAuthMiddleware::class,
		  RoleMiddleware::class.':admin,hrd',
	 ])->group(function () {
		  Route::get('/pegawai', [PegawaiController::class, 'index']);
		  Route::post('/pegawai', [PegawaiController::class, 'store']);
	 });

Penjelasan:

1. JwtAuthMiddleware wajib supaya endpoint butuh token.
2. RoleMiddleware menentukan role yang boleh akses endpoint.
3. Kalau role tidak sesuai, akan dapat Access denied.

## 3.3 Buat Form Request untuk validasi

Untuk GET yang punya filter, tetap bagus pakai Form Request supaya query param terstruktur.

Contoh:

1. Buat file request di [app/Http/Requests](app/Http/Requests), misalnya PegawaiIndexRequest.
2. Definisikan rules seperti:
	- search nullable string max 100
	- page nullable integer min 1
	- per_page nullable integer min 1 max 100

Untuk POST atau PUT, Form Request wajib karena ada input yang harus divalidasi.

## 3.4 Buat Controller untuk handling endpoint

Lokasi controller API:
[app/Http/Controllers/Api](app/Http/Controllers/Api)

Tugas controller:

1. Ambil data request yang sudah valid.
2. Ambil claim user dari request (hasil middleware JWT).
3. Kirim parameter ke service.
4. Kembalikan response JSON.

Contoh flow method index:

	 public function index(PegawaiIndexRequest $request): JsonResponse
	 {
		  $result = $this->pegawaiService->index(
				filters: $request->validated(),
				actor: $request->input('_jwt_claims', [])
		  );

		  return response()->json([
				'success' => true,
				'message' => 'Data pegawai berhasil diambil.',
				'data' => $result,
		  ]);
	 }

## 3.5 Letak pengambilan data dari database

Pengambilan data dikerjakan di Repository, bukan di Controller.

Lokasi:
[app/Repositories](app/Repositories)

Contoh konsep method repository:

1. Menerima filter dari service.
2. Build query Eloquent dari model.
3. Tambah where jika filter terisi.
4. Return data atau pagination.

Contoh alur query:

	 $query = Pegawai::query()->with(['pegawaiPribadi', 'pegawaiPekerjaan']);

	 if (!empty($filters['search'])) {
		  $query->where('nama', 'like', '%'.$filters['search'].'%');
	 }

	 return $query->paginate($perPage);

Model yang dipakai ada di:
[app/Models/Pegawai.php](app/Models/Pegawai.php)

## 3.6 Letak processing logika bisnis

Semua logika bisnis dikerjakan di Service.

Lokasi:
[app/Services](app/Services)

Contoh logic di service:

1. Menentukan default per_page.
2. Membersihkan filter.
3. Validasi bisnis tambahan (di luar validasi format input).
4. Mapping data sebelum output.

Contoh:

	 $perPage = min((int) ($filters['per_page'] ?? 10), 100);
	 $data = $this->pegawaiRepository->paginate($filters, $perPage);

	 return [
		  'items' => $data->items(),
		  'meta' => [
				'current_page' => $data->currentPage(),
				'last_page' => $data->lastPage(),
				'per_page' => $data->perPage(),
				'total' => $data->total(),
		  ],
	 ];

## 3.7 Letak set output endpoint

Output final disusun di Controller (response JSON).

Aturan response project:

Sukses:

	 {
		"success": true,
		"message": "Pesan sukses",
		"data": {}
	 }

Error:

	 {
		"success": false,
		"message": "Pesan error"
	 }

Jadi, data dari service dimasukkan ke field data di controller.

## 4. Cara Menentukan Request GET Butuh Data Apa Saja

Gunakan checklist ini saat desain endpoint GET:

1. Data utama apa yang ditampilkan.
2. Perlu pagination atau tidak.
3. Perlu search keyword atau tidak.
4. Perlu filter by status, unit, jabatan, tanggal, atau tidak.
5. Perlu sorting atau tidak.
6. Role mana yang boleh akses data ini.
7. Wajib token atau public endpoint.

Best practice GET:

1. Query param untuk filter.
2. Jangan pakai body untuk GET.
3. Batasi per_page maksimum agar aman.
4. Gunakan eager loading untuk relasi yang ditampilkan.

## 5. Kalau Mau Tambah Variabel Baru, Masuk di Mana?

Contoh variabel baru: kategori.

Urutan update yang benar:

1. Form Request:
	Tambahkan rules kategori.

2. Controller:
	Pastikan kategori ikut diambil dari validated input.

3. Service:
	Tambahkan logika kategori, misalnya default value atau mapping.

4. Repository:
	Tambahkan kondisi query berdasarkan kategori.

5. Model atau Migration (jika variabel adalah kolom baru DB):
	- Jika kolom baru, buat migration.
	- Update fillable dan casts di model terkait.

6. Response:
	Jika perlu tampilkan kategori ke client, masukkan ke data output.

7. Test:
	Tambah test untuk skenario dengan kategori.

## 6. Kapan Token Wajib, Kapan Tidak?

1. Endpoint login atau health check biasanya tidak wajib token.
2. Endpoint data pegawai internal wajib token.
3. Endpoint sensitif wajib token + role.

Pola middleware:

1. Token saja:
	JwtAuthMiddleware::class

2. Token + role:
	JwtAuthMiddleware::class + RoleMiddleware::class dengan daftar role.

## 7. Contoh Ringkas Satu Alur Endpoint GET

Kasus:

1. Endpoint: GET /api/pegawai
2. Role: admin, hrd
3. Query: search, page, per_page

Alurnya:

1. Route menerima GET /api/pegawai.
2. JwtAuthMiddleware cek token.
3. RoleMiddleware cek role admin atau hrd.
4. PegawaiIndexRequest validasi query param.
5. Controller kirim filter ke service.
6. Service atur default dan logika bisnis.
7. Repository query model Pegawai dan relasi.
8. Service mapping hasil pagination.
9. Controller kirim JSON success + data.

## 8. Checklist Sebelum Endpoint Dibilang Selesai

1. Route sudah ada dan benar method-nya.
2. Middleware token dan role sudah sesuai kebutuhan.
3. Validasi request sudah dibuat.
4. Query database tidak ditulis di controller.
5. Logika bisnis berada di service.
6. Response JSON sudah konsisten.
7. Test skenario sukses dan gagal sudah dibuat.

## 9. Struktur Praktis yang Disarankan per Fitur

Saat bikin fitur endpoint baru, minimal siapkan:

1. Controller di [app/Http/Controllers/Api](app/Http/Controllers/Api)
2. Service di [app/Services](app/Services)
3. Repository di [app/Repositories](app/Repositories)
4. Form Request di [app/Http/Requests](app/Http/Requests)
5. Route di [routes/api.php](routes/api.php)
6. Test di [tests/Feature/Api](tests/Feature/Api)

Dengan pola ini, endpoint tetap bersih, aman, dan gampang di-maintain.

## 10. Implementasi Endpoint GET /api/dashboard

Target endpoint:

1. Method: GET
2. URL: /api/dashboard
3. Wajib token JWT
4. Return utama: Selamat datang (nama role)

### 10.1 Langkah 1 - Tambah route

File yang diubah:
[routes/api.php](routes/api.php)

Tambahkan import controller:

	 use App\Http\Controllers\Api\DashboardController;

Tambahkan route endpoint dashboard:

	 Route::middleware([
		  JwtAuthMiddleware::class,
		  RoleMiddleware::class.':admin,pegawai,hrd,direktur',
	 ])->get('/dashboard', [DashboardController::class, 'show']);

Kenapa pakai middleware itu:

1. JwtAuthMiddleware untuk memastikan request membawa token valid.
2. RoleMiddleware untuk batasi role yang boleh akses dashboard.

### 10.2 Langkah 2 - Buat controller dashboard

File baru:
[app/Http/Controllers/Api/DashboardController.php](app/Http/Controllers/Api/DashboardController.php)

Isi pentingnya:

1. Ambil claims JWT dari _jwt_claims.
2. Ambil nilai role dari claims.
3. Tentukan return per role lewat array konfigurasi.
4. Return JSON:
	- success true
	- message "Selamat datang (role)"
	- data role dan data dashboard

### 10.3 Lokasi konfigurasi return masing-masing role

Tempat utamanya ada di file:
[app/Http/Controllers/Api/DashboardController.php](app/Http/Controllers/Api/DashboardController.php)

Cari blok ini:

	 // Konfigurasi return per role: ubah isi array ini sesuai kebutuhan output masing-masing role.
	 $dashboardByRole = [
		  'admin' => [...],
		  'pegawai' => [...],
		  'hrd' => [...],
		  'direktur' => [...],
	 ];

Kalau mau ubah return per role, edit isi tiap key role tersebut.

Contoh ubah pesan welcome:

	 'admin' => [
		  'welcome' => 'Selamat datang admin super',
		  'summary' => [...],
	 ]

### 10.4 Cara atur return kalau data diambil dari database

Supaya rapi, best practice tetap:

1. Query database di Repository.
2. Logic gabung data di Service.
3. Controller hanya menyusun response JSON.

Alur yang disarankan:

1. Controller kirim role dan identitas user ke service.
2. Service tentukan data apa saja berdasarkan role.
3. Service panggil repository untuk ambil data DB.
4. Service return payload siap tampil.
5. Controller kirim ke response data.

Contoh konsep service (pseudo):

	 public function getDashboard(string $role, array $claims): array
	 {
		  return match ($role) {
			   'admin' => [
					 'welcome' => 'Selamat datang admin',
					 'summary' => [
						  'total_pegawai' => $this->pegawaiRepository->countAll(),
						  'total_unit' => $this->unitKerjaRepository->countAll(),
					 ],
			   ],
			   'pegawai' => [
					 'welcome' => 'Selamat datang pegawai',
					 'summary' => [
						  'profil' => $this->pegawaiRepository->findByNik((string) ($claims['sub'] ?? '')),
						  'jadwal_diklat' => $this->diklatRepository->getUpcomingByPegawaiId((int) ($claims['pegawai_id'] ?? 0)),
					 ],
			   ],
			   default => [
					 'welcome' => 'Access denied.',
					 'summary' => [],
			   ],
		  };
	 }

### 10.5 Cara menentukan data apa saja yang mau ditampilkan

Gunakan checklist ini per role:

1. Role ini butuh lihat data ringkasan apa.
2. Data itu sumbernya tabel mana.
3. Perlu detail penuh atau cukup agregasi.
4. Data sensitif mana yang harus disembunyikan.
5. Batas jumlah data di dashboard (hindari query berat).
6. Kapan perlu cache agar response cepat.

Contoh struktur output dashboard yang aman dan jelas:

	 {
		  "success": true,
		  "message": "Selamat datang admin",
		  "data": {
			   "role": "admin",
			   "dashboard": {
					 "total_pegawai": 123,
					 "total_unit": 8,
					 "notifikasi": 5
			   }
		  }
	 }

### 10.6 Uji endpoint dashboard

Request:

	 GET /api/dashboard

Header wajib:

	 Authorization: Bearer <token_jwt>

Expected:

1. Token valid + role valid -> success true dan message "Selamat datang (role)".
2. Token tidak valid -> status 401.
3. Role tidak diizinkan -> status 403.
