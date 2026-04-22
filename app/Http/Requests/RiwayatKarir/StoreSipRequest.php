<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_sip_id' => ['nullable', 'exists:jenis_sip,id'],
            'nomor_sip' => ['required', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tanggal_terbit'],
            'is_current' => ['required', 'boolean'],
            'sk_sip' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
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
