<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date|before_or_equal:today',
            'class_id' => 'required|exists:classes,id',
            'session' => 'in:pagi,siang',
            'details' => 'required|array|min:1',
            'details.*.status' => 'required|in:hadir,sakit,izin,alfa,terlambat',
            'details.*.check_in' => 'nullable|date_format:H:i',
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