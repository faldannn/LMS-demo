<?php
namespace App\Http\Requests;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student?->id;
        $userId = $student?->user_id;
        $restoredStudent = $studentId ? null : Student::onlyTrashed()
            ->where(function ($query) {
                $query->where('nis', $this->input('nis'));

                if ($this->filled('nisn')) {
                    $query->orWhere('nisn', $this->input('nisn'));
                }
            })
            ->first();
        $restoredUserId = $restoredStudent
            ? User::where('username', $this->input('nisn') ?: $this->input('nis'))->value('id')
            : null;

        return [
            'nis' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'nis')->whereNull('deleted_at')->ignore($studentId),
                Rule::unique('users', 'username')->ignore($userId ?: $restoredUserId),
            ],
            'nisn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'nisn')->whereNull('deleted_at')->ignore($studentId),
            ],
            'rfid' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'rfid')->whereNull('deleted_at')->ignore($studentId),
            ],
            'name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'dob' => 'nullable|date|before:today',
            'pob' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'registration_type' => 'nullable|in:baru,pindahan',
            'entry_date' => 'nullable|date',
            'origin_school' => 'nullable|string|max:150',
            'student_status' => 'nullable|in:aktif,lulus,mutasi_keluar,drop_out,non_aktif',
            'exit_date' => 'nullable|date',
            'exit_reason' => 'nullable|string|max:255',
            'destination_school' => 'nullable|string|max:150',
            'class_id' => 'required|exists:classes,id',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:100',
            'photo' => \App\Support\UploadRules::ruleString('image') . '|nullable',
            'status' => 'boolean',
            'password' => 'nullable|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'nis.unique' => 'NIS ini sudah terdaftar pada data siswa atau akun pengguna.',
            'nisn.required' => 'NISN wajib diisi sebagai username login siswa.',
            'nisn.unique' => 'NISN ini sudah terdaftar.',
        ];
    }
}
