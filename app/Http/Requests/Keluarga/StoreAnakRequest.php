<?php

namespace App\Http\Requests\Keluarga;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnakRequest extends FormRequest
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
            'jenis_kelamin' => ['nullable', 'string', 'in:L,P'],
            'status_anak' => ['nullable', 'string', 'max:255'],
            'pendidikan_terakhir' => ['nullable', 'string', 'max:255'],
            'status_tanggungan' => ['nullable', 'boolean'],
            'usia' => ['nullable', 'integer'],
            'keterangan_disabilitas' => ['nullable', 'string', 'max:255'],
            'akta_kelahiran_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
