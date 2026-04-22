<?php

namespace App\Http\Requests\Diklat;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePegawaiDiklatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kategori' => ['sometimes', 'nullable', 'string', 'max:100'],
            'jenis_diklat' => ['sometimes', 'nullable', 'string', 'max:100'],
            'penyelenggara' => ['sometimes', 'nullable', 'string', 'max:255'],
            'lokasi' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['sometimes', 'nullable', 'date'],
            'tanggal_selesai' => ['sometimes', 'nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'no_sertif' => ['sometimes', 'nullable', 'string', 'max:100'],
            'upload_sertif' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'jp' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'jenis_biaya' => ['sometimes', 'nullable', 'string', 'max:100'],
            'total_biaya' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'catatan' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'jenis_pelaksana' => ['sometimes', 'nullable', 'in:internal,external'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('jenis_pelaksana')) {
            $this->merge([
                'jenis_pelaksana' => strtolower((string) $this->input('jenis_pelaksana')),
            ]);
        }
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
