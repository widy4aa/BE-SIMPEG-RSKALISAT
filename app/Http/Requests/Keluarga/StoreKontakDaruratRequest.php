<?php

namespace App\Http\Requests\Keluarga;

use Illuminate\Foundation\Http\FormRequest;

class StoreKontakDaruratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kontak' => ['required', 'string', 'max:255'],
            'hubungan_keluarga' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
