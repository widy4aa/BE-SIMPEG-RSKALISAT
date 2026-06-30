<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unit_kerja_id' => ['sometimes', 'nullable', 'exists:unit_kerja,id'],
            'nama_jabatan' => ['sometimes', 'required', 'string', 'max:255'],
            'tmt_mulai' => ['sometimes', 'nullable', 'date'],
            'tmt_selesai' => ['sometimes', 'nullable', 'date', 'after_or_equal:tmt_mulai'],
            'sk_jabatan' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
            'note' => ['sometimes', 'nullable', 'string'],
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
