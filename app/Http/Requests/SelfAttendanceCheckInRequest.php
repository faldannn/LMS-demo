<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SelfAttendanceCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->student !== null;
    }

    public function rules(): array
    {
        return [
            'qr_code' => 'nullable|string|max:2000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'face_detection_confidence' => 'nullable|numeric|min:0|max:1',
            'face_match_confidence' => 'nullable|numeric|min:0|max:1',
            'face_matched' => 'nullable|boolean',
            'check_in_mode' => ['nullable', Rule::in(['qr_scan', 'qr_class', 'qr', 'full', 'gps_fallback', 'camera_fallback', 'manual_fallback'])],
            'fallback_reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:5120',
            'landmark_descriptor' => 'nullable|array',
            'landmark_descriptor.*' => 'numeric',
        ];
    }
}
