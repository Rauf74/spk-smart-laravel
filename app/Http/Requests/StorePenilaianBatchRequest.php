<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jawaban'   => 'required|array',
            'jawaban.*' => 'exists:subkriterias,id_subkriteria',
            'id_user'   => 'nullable|exists:users,id_user',
        ];
    }

    public function messages(): array
    {
        return [
            'jawaban.required'   => 'Jawaban wajib diisi.',
            'jawaban.array'      => 'Format jawaban tidak valid.',
            'jawaban.*.exists'   => 'Pilihan jawaban tidak ditemukan.',
            'id_user.exists'     => 'Siswa tidak ditemukan.',
        ];
    }
}
