<?php

namespace App\Http\Requests\Hrd;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePegawaiIntiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik'               => ['sometimes', 'string', 'max:20'],
            'nip'               => ['sometimes', 'nullable', 'string', 'max:20'],
            'nama'              => ['sometimes', 'string', 'max:255'],
            'jenis_pegawai_id'  => ['sometimes', 'nullable', 'exists:jenis_pegawai,id'],
            'profesi_id'        => ['sometimes', 'nullable', 'exists:profesi,id'],
            'jabatan_id'        => ['sometimes', 'nullable', 'exists:jabatan,id'],
            'golongan_ruang_id' => ['sometimes', 'nullable', 'exists:golongan_ruang,id'],
            'pangkat_id'        => ['sometimes', 'nullable', 'exists:pangkat,id'],
            'status_pegawai'    => ['sometimes', 'string', 'in:aktif,nonaktif,cuti'],
            'tgl_masuk'         => ['sometimes', 'nullable', 'date'],
            'tmt_cpns'          => ['sometimes', 'nullable', 'date'],
            'tmt_pns'           => ['sometimes', 'nullable', 'date'],
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
