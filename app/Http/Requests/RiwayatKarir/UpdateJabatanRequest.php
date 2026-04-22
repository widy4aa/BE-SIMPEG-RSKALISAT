<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jabatan' => ['sometimes', 'required', 'string', 'max:255'],
            'is_current' => ['sometimes', 'required', 'boolean'],
            'tmt_mulai' => ['sometimes', 'nullable', 'date'],
            'tmt_selesai' => ['sometimes', 'nullable', 'date', 'after_or_equal:tmt_mulai'],
            'sk_jabatan' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'note' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
