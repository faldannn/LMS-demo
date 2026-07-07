<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $id = $user ? $user->id : null;

        return [
            'name' => 'required|string|max:100',
            'email' => "nullable|email|unique:users,email,{$id}",
            'role' => 'required|in:kepala_sekolah,guru,siswa',
            'password' => [
                'nullable',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers(),
            ],
            'username' => "sometimes|required|string|max:50|unique:users,username,{$id}|alpha_dash",
        ];
    }
}
