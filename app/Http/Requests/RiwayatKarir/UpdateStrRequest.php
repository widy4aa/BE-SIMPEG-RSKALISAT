<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_str' => ['sometimes', 'required', 'string', 'max:255'],
            'tanggal_terbit' => ['sometimes', 'required', 'date'],
            'tanggal_kadaluarsa' => ['sometimes', 'nullable', 'date', 'after_or_equal:tanggal_terbit'],
            'is_current' => ['sometimes', 'required', 'boolean'],
            'sk_str' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB max
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
