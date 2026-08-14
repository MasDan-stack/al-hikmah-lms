<?php

namespace App\Http\Requests\Admin;

use App\Models\MentorAvailability;
use Illuminate\Foundation\Http\FormRequest;

class AssignStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'mentor_id' => ['required', 'integer', 'exists:mentors,id'],
            'day' => ['required', 'string', 'in:'.implode(',', MentorAvailability::DAYS_ORDER)],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Santri wajib dipilih.',
            'student_id.exists' => 'Data santri tidak valid.',
            'mentor_id.required' => 'Mentor wajib dipilih.',
            'mentor_id.exists' => 'Data mentor tidak valid.',
            'day.required' => 'Hari belajar wajib dipilih.',
            'day.in' => 'Pilihan hari tidak valid.',
        ];
    }
}
