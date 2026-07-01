<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kriteriaId = $this->route('id'); // Ambil ID dari route parameter

        return [
            'kode_kriteria'  => 'required|string|max:20|unique:kriterias,kode_kriteria,' . $kriteriaId . ',id_kriteria',
            'nama_kriteria'  => 'required|string|max:100',
            'jenis'          => 'required|in:Benefit,Cost',
            'bobot'          => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_kriteria.required' => 'Kode kriteria wajib diisi.',
            'kode_kriteria.unique'   => 'Kode kriteria sudah dipakai.',
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'jenis.required'         => 'Jenis kriteria wajib dipilih.',
            'jenis.in'               => 'Jenis harus Benefit atau Cost.',
            'bobot.required'         => 'Bobot wajib diisi.',
            'bobot.numeric'          => 'Bobot harus berupa angka.',
            'bobot.min'              => 'Bobot minimal 0.',
            'bobot.max'              => 'Bobot maksimal 100.',
        ];
    }
}
