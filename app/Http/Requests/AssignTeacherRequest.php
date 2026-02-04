<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $teacherId = $this->route('teacher');
        
        return [
            'class_id' => [
                'required',
                'exists:classes,id',
                Rule::unique('teacher_class_assignments')
                    ->where(function ($query) use ($teacherId) {
                        return $query->where('teacher_id', $teacherId)
                                   ->where('subject', $this->subject);
                    }),
            ],
            'subject' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'class_id.unique' => 'This teacher is already assigned to teach this subject in the selected class.',
        ];
    }
}