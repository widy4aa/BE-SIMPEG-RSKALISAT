<?php

namespace App\Http\Requests\Hrd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePegawaiPribadiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_kelamin'       => ['sometimes', 'nullable', 'string', 'in:L,P,Laki-laki,Perempuan'],
            'tanggal_lahir'       => ['sometimes', 'nullable', 'date'],
            'agama'               => ['sometimes', 'nullable', 'string', 'max:50'],
            'status_perkawinan'   => ['sometimes', 'nullable', 'string', 'max:50'],
            'alamat'              => ['sometimes', 'nullable', 'string'],
            'no_telp'             => ['sometimes', 'nullable', 'string', 'max:20'],
            'pendidikan_terakhir' => ['sometimes', 'nullable', 'string', 'max:100'],
            'no_kk'               => ['sometimes', 'nullable', 'string', 'max:20'],
            'foto_profil'         => ['sometimes', 'nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'ktp_file'            => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'kk_file'             => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
