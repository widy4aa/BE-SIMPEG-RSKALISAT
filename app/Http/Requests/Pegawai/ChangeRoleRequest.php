<?php

namespace App\Http\Requests\Pegawai;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role' => 'sometimes|required|string|in:pegawai,admin,hrd,direktur',
            'status_pegawai' => 'sometimes|required|string|in:aktif,tidak aktif',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->hasAny(['role', 'status_pegawai'])) {
                $validator->errors()->add('payload', 'Minimal role atau status_pegawai harus diisi.');
            }
        });
    }
}
