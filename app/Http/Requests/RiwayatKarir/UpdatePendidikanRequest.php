<?php

namespace App\Http\Requests\RiwayatKarir;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePendidikanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenjang' => ['sometimes', 'required', 'string', 'max:50'],
            'institusi' => ['sometimes', 'required', 'string', 'max:255'],
            'jurusan' => ['sometimes', 'required', 'string', 'max:255'],
            'tahun_lulus' => ['sometimes', 'required', 'integer', 'min:1900', 'max:2100'],
            'nomor_ijazah' => ['nullable', 'string', 'max:100'],
            'ijazah' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
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
