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
