<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_user' => 'required|exists:users,id_user',
            'catatan' => 'required|string|min:3|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'id_user.required' => 'Siswa wajib dipilih.',
            'id_user.exists'   => 'Siswa tidak ditemukan.',
            'catatan.required' => 'Catatan wajib diisi.',
            'catatan.min'      => 'Catatan minimal 3 karakter.',
            'catatan.max'      => 'Catatan maksimal 2000 karakter.',
        ];
    }
}
