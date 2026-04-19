<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['sometimes', 'nullable', 'string', 'max:30'],
            'nik' => ['sometimes', 'nullable', 'string', 'max:30'],
            'nama' => ['sometimes', 'nullable', 'string', 'max:255'],
            'profesi' => ['sometimes', 'nullable', 'string', 'max:100'],
            'jenis_pegawai' => ['sometimes', 'nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['sometimes', 'nullable', 'in:L,P,l,p'],
            'tanggal_lahir' => ['sometimes', 'nullable', 'date'],
            'agama' => ['sometimes', 'nullable', 'string', 'max:50'],
            'status_kawin' => ['sometimes', 'nullable', 'string', 'max:50'],
            'alamat' => ['sometimes', 'nullable', 'string', 'max:500'],
            'no_telp' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'status_pegawai' => ['sometimes', 'nullable', 'string', 'max:50'],
            'tgl_masuk' => ['sometimes', 'nullable', 'date'],
            'tmt_cpns' => ['sometimes', 'nullable', 'date'],
            'tmt_pns' => ['sometimes', 'nullable', 'date'],
            'note' => ['sometimes', 'nullable', 'string', 'max:1000'],
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $editableFields = [
                'nip',
                'nik',
                'nama',
                'profesi',
                'jenis_pegawai',
                'jenis_kelamin',
                'tanggal_lahir',
                'agama',
                'status_kawin',
                'alamat',
                'no_telp',
                'email',
                'status_pegawai',
                'tgl_masuk',
                'tmt_cpns',
                'tmt_pns',
            ];

            $hasAnyField = false;
            foreach ($editableFields as $field) {
                if ($this->has($field)) {
                    $hasAnyField = true;
                    break;
                }
            }

            if (! $hasAnyField) {
                $validator->errors()->add('payload', 'Minimal satu field profile harus diisi.');
            }
        });
    }
}
