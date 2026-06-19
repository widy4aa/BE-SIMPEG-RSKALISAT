<?php

namespace App\Http\Requests\Keluarga;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTanggunganLainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'              => ['sometimes', 'required', 'string', 'max:255'],
            'hubungan_keluarga' => ['sometimes', 'required', 'string', 'max:100'],
            'status_tanggungan' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
