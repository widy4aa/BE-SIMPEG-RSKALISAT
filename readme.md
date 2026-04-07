# BE-SIMPEG-RSKALISAT

Backend API HRIS berbasis Laravel.

Info Laravel (singkat):
- Framework: Laravel 13
- PHP: 8.3+

## Menjalankan Project

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

## Endpoint Ready

Base URL lokal:
- `http://127.0.0.1:8000`

1. `GET /api/health`

Contoh response 200:
```json
{
	"success": true,
	"message": "API is running",
	"data": {
		"status": "up"
	}
}
```

2. `POST /api/login`

Request body:
```json
{
	"nik": "3174010101010099",
	"password": "password"
}
```

Contoh response sukses 200:
```json
{
	"success": true,
	"message": "Login berhasil.",
	"data": {
		"token_type": "Bearer",
		"access_token": "<jwt_token>",
		"expires_in": 43200,
		"user": {
			"id": 1,
			"nik": "3174010101010099",
			"role": "admin",
			"nama": "Admin SIMPEG"
		}
	}
}
```

Contoh response error 422 (validasi):
```json
{
	"success": false,
	"message": "Validasi gagal.",
	"errors": {
		"nik": [
			"NIK wajib diisi."
		],
		"password": [
			"Password wajib diisi."
		]
	}
}
```

Contoh response error 401 (kredensial salah):
```json
{
	"success": false,
	"message": "NIK atau password tidak valid."
}
```

Contoh response error 403 (akun nonaktif):
```json
{
	"success": false,
	"message": "Akun tidak aktif. Silakan hubungi admin."
}
```

## Akun Seeder Login

- Admin: nik `3174010101010099`, password `password`
- HRD: nik `3174010101010098`, password `password`
- Direktur: nik `3174010101010003`, password `password`
- Pegawai: nik `3174010101010001`, password `password`
- Pegawai: nik `3174010101010002`, password `password`

## Log Terakhir

- 2026-04-07: Implementasi migration-model-seeder HRIS dari ERD.
- 2026-04-07: Penambahan endpoint login JWT `POST /api/login`.
- 2026-04-07: Seeder diperbarui, semua role login menggunakan NIK.
