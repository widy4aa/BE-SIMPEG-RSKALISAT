<?php

namespace App\Http\Requests\Keluarga;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKontakDaruratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kontak' => ['sometimes', 'required', 'string', 'max:255'],
            'hubungan_keluarga' => ['sometimes', 'required', 'string', 'max:255'],
            'nomor_hp' => ['sometimes', 'required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
