<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePenugasanKlinisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_surat' => ['sometimes', 'required', 'string', 'max:255'],
            'tgl_mulai' => ['sometimes', 'required', 'date'],
            'tgl_kadaluarsa' => ['sometimes', 'nullable', 'date', 'after_or_equal:tgl_mulai'],
            'is_current' => ['sometimes', 'required', 'boolean'],
            'dokumen_file' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
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
