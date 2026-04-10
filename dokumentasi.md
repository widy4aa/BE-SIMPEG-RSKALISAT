# Dokumentasi API BE-SIMPEG-RSKALISAT

Dokumen ini berisi daftar endpoint API yang tersedia pada project backend ini.


## Format Response Standar

### Sukses

```json
{
  "success": true,
  "message": "Pesan sukses",
  "data": {}
}
```

### Gagal

```json
{
  "success": false,
  "message": "Pesan error"
}
```

## Authentication

Endpoint yang dilindungi middleware JWT wajib mengirim header:

```http
Authorization: Bearer <jwt_token>
```

## Daftar Endpoint

### 1. Health Check

- Method: `GET`
- URL: `/api/health`
- Auth: Tidak perlu

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "API is running",
  "data": {
    "status": "up"
  }
}
```

### 2. Login

- Method: `POST`
- URL: `/api/login`
- Auth: Tidak perlu

Request body:

```json
{
  "nik": "3174010101010099",
  "password": "password"
}
```

Validasi request:

- `nik`: wajib, string, maksimal 30 karakter
- `password`: wajib, string, minimal 6 karakter

Contoh response `200 OK`:

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

Contoh response `422 Unprocessable Entity` (validasi gagal):

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

Contoh response `401 Unauthorized` (kredensial salah):

```json
{
  "success": false,
  "message": "NIK atau password tidak valid."
}
```

Contoh response `403 Forbidden` (akun tidak aktif):

```json
{
  "success": false,
  "message": "Akun tidak aktif. Silakan hubungi admin."
}
```

### 3. Cek Role Login

- Method: `GET`
- URL: `/api/role`
- Auth: Wajib Bearer token
- Role yang diizinkan: `admin`, `pegawai`, `hrd`, `direktur`

Contoh header:

```http
Authorization: Bearer <jwt_token>
```

Contoh response `200 OK`:

```json
{
  "success": true,
  "message": "Selamat datang admin.",
  "data": {
    "role": "admin"
  }
}
```

Kemungkinan response berdasarkan role:

- `admin` -> `Selamat datang admin.`
- `pegawai` -> `Selamat datang pegawai.`
- `hrd` -> `Selamat datang hrd.`
- `direktur` -> `Selamat datang direktur.`

Contoh response `401 Unauthorized` (token tidak valid/tidak ada):

```json
{
  "success": false,
  "message": "Access denied."
}
```

Contoh response `403 Forbidden` (role tidak diizinkan):

```json
{
  "success": false,
  "message": "Access denied."
}
```

## Akun Seeder Untuk Uji Login

- Admin: `3174010101010099` / `password`
- HRD: `3174010101010098` / `password`
- Direktur: `3174010101010003` / `password`
- Pegawai: `3174010101010001` / `password`
- Pegawai: `3174010101010002` / `password`

## Quick Test via cURL

Login:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d "{\"nik\":\"3174010101010099\",\"password\":\"password\"}"
```

Health check:

```bash
curl http://127.0.0.1:8000/api/health
```

Cek role (ganti token):

```bash
curl http://127.0.0.1:8000/api/role \
  -H "Authorization: Bearer <jwt_token>"
```
