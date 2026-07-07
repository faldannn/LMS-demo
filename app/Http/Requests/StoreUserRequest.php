<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isKepalaSekolah();
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:users|alpha_dash',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users',
            'role' => 'required|in:kepala_sekolah,guru,siswa',
        ];
    }
}
