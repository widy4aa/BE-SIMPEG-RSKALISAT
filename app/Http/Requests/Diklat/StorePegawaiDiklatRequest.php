<?php

namespace App\Http\Requests\Diklat;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePegawaiDiklatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'jenis_diklat' => ['required', 'string', 'max:100'],
            'penyelenggara' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'no_sertif' => ['nullable', 'string', 'max:100'],
            'upload_sertif' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'jp' => ['required', 'integer', 'min:1'],
            'jenis_biaya' => ['required_if:jenis_pelaksana,internal', 'nullable', 'string', 'max:100'],
            'total_biaya' => ['required_if:jenis_pelaksana,internal', 'nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'jenis_pelaksana' => ['required', 'in:internal,external'],
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
