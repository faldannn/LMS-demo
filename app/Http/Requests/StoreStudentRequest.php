<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;
        return [
            'nis' => "required|string|max:20|unique:students,nis,{$studentId}",
            'nisn' => "nullable|string|max:20|unique:students,nisn,{$studentId}",
            'name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'dob' => 'nullable|date|before:today',
            'pob' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'nullable|email|max:100',
            'photo' => \App\Support\UploadRules::ruleString('image') . '|nullable',
            'status' => 'boolean',
        ];
    }
}