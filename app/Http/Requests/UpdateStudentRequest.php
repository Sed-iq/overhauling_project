<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSchoolAdmin() || $this->user()->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $student = $this->route('student');
        
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->where(function ($query) {
                        return $query->where('school_id', $this->user()->school_id);
                    })
                    ->ignore($student->user_id),
            ],
            'password' => ['sometimes', 'string', 'min:8'],
            'class_id' => ['sometimes', 'nullable', 'exists:classes,id'],
            'student_id' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('students', 'student_id')
                    ->where(function ($query) {
                        return $query->where('school_id', $this->user()->school_id);
                    })
                    ->ignore($student->id),
            ],
            'date_of_birth' => ['sometimes', 'date', 'before:today'],
            'guardian_name' => ['sometimes', 'string', 'max:255'],
            'guardian_phone' => ['sometimes', 'string', 'max:20'],
            'address' => ['sometimes', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'This email is already registered in your school.',
            'student_id.unique' => 'This student ID is already used in your school.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
        ];
    }
}