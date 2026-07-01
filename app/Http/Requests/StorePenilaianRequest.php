<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_kriteria'     => 'required|exists:kriterias,id_kriteria',
            'id_pertanyaan'   => 'required|exists:pertanyaans,id_pertanyaan',
            'id_alternatif'   => 'required|exists:alternatifs,id_alternatif',
            'id_subkriteria'  => 'required|exists:subkriterias,id_subkriteria',
            'jawaban'         => 'required|numeric|min:0|max:5',
            '_partial'        => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id_kriteria.required'    => 'Kriteria wajib dipilih.',
            'id_kriteria.exists'      => 'Kriteria tidak ditemukan.',
            'id_pertanyaan.required'  => 'Pertanyaan wajib dipilih.',
            'id_pertanyaan.exists'    => 'Pertanyaan tidak ditemukan.',
            'id_alternatif.required'  => 'Program studi wajib dipilih.',
            'id_alternatif.exists'    => 'Program studi tidak ditemukan.',
            'id_subkriteria.required' => 'Pilihan jawaban wajib dipilih.',
            'id_subkriteria.exists'   => 'Pilihan jawaban tidak ditemukan.',
            'jawaban.required'        => 'Jawaban wajib diisi.',
            'jawaban.numeric'         => 'Jawaban harus berupa angka.',
            'jawaban.min'             => 'Jawaban minimal 0.',
            'jawaban.max'             => 'Jawaban maksimal 5.',
        ];
    }
}
