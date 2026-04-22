<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Foundation\Http\FormRequest;

class StoreJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jabatan' => ['required', 'string', 'max:255'],
            'is_current' => ['required', 'boolean'],
            'tmt_mulai' => ['nullable', 'date'],
            'tmt_selesai' => ['nullable', 'date', 'after_or_equal:tmt_mulai'],
            'sk_jabatan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'note' => ['nullable', 'string'],
        ];
    }
}
