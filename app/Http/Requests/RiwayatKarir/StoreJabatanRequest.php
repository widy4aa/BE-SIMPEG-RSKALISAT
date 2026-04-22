<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_kerja_id' => ['nullable', 'exists:unit_kerja,id'],
            'nama_jabatan' => ['required', 'string', 'max:255'],
            'is_current' => ['required', 'boolean'],
            'tmt_mulai' => ['nullable', 'date'],
            'tmt_selesai' => ['nullable', 'date', 'after_or_equal:tmt_mulai'],
            'sk_jabatan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'note' => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
