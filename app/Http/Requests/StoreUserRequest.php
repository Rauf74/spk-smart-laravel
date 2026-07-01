<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization sudah dihandle middleware role
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nama_user' => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users,username',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:Guru BK,Siswa',
            'nis'       => 'nullable|numeric|unique:users,nis',
        ];
    }

    /**
     * Custom error messages (Bahasa Indonesia).
     */
    public function messages(): array
    {
        return [
            'nama_user.required' => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah dipakai, pilih yang lain.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 6 karakter.',
            'role.required'      => 'Role wajib dipilih.',
            'role.in'            => 'Role harus Guru BK atau Siswa.',
            'nis.numeric'        => 'NIS harus berupa angka.',
            'nis.unique'         => 'NIS sudah dipakai, pilih yang lain.',
        ];
    }
}
