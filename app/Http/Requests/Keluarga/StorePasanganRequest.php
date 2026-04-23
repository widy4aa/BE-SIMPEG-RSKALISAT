<?php

namespace App\Http\Requests\Keluarga;

use Illuminate\Foundation\Http\FormRequest;

class StorePasanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'status_pernikahan' => ['nullable', 'string', 'max:255'],
            'tanggal_pernikahan' => ['nullable', 'date'],
            'nomor_buku_nikah' => ['nullable', 'string', 'max:255'],
            'status_tanggungan' => ['nullable', 'boolean'],
            'npwp_pasangan' => ['nullable', 'string', 'max:255'],
            'buku_nikah_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
