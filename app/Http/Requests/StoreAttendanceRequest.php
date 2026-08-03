<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->isStaff() || $user->isAdmin() || $user->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date|before_or_equal:today',
            'class_id' => 'required|exists:classes,id',
            'session' => 'nullable|string|max:50',
            'details' => 'required|array|min:1',
            'details.*.status' => 'required|in:hadir,sakit,izin,alfa,terlambat',
            'details.*.check_in' => 'nullable',
            'details.*.notes' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'date.before_or_equal' => 'Tidak dapat menginput absensi untuk tanggal yang akan datang.',
            'details.required' => 'Data absensi siswa wajib diisi.',
        ];
    }
}