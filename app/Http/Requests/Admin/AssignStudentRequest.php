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
        $allowedDays = array_unique(array_merge(
            MentorAvailability::DAYS_ORDER,
            array_keys(MentorAvailability::INDONESIAN_TO_ENGLISH)
        ));

        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'mentor_id' => ['required', 'integer', 'exists:mentors,id'],
            'day' => ['required', 'string', 'in:'.implode(',', $allowedDays)],
            'slot_number' => ['nullable', 'integer', 'between:0,6'],
            'slot' => ['nullable', 'integer', 'between:0,6'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'notes' => ['nullable', 'string', 'max:255'],
            'time' => ['nullable', 'string'],
            'time_assigned' => ['nullable', 'string'],
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
            'slot_number.between' => 'Pilihan angka slot harus antara 0 s/d 6.',
        ];
    }
}
