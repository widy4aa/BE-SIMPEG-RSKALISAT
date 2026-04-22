<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_sip_id' => ['sometimes', 'nullable', 'exists:jenis_sip,id'],
            'nomor_sip' => ['sometimes', 'required', 'string', 'max:255'],
            'tanggal_terbit' => ['sometimes', 'required', 'date'],
            'tanggal_kadaluarsa' => ['sometimes', 'nullable', 'date', 'after_or_equal:tanggal_terbit'],
            'is_current' => ['sometimes', 'required', 'boolean'],
            'sk_sip' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
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
