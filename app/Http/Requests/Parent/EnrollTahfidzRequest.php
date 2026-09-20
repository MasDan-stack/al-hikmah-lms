<?php

namespace App\Http\Requests\Parent;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EnrollTahfidzRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->isParent() && $user->parentProfile !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|string',
            'new_nama_anak' => 'required_if:student_id,new|nullable|string|max:255',
            'new_usia' => 'nullable|integer|min:3|max:80',
            'new_gender' => 'nullable|string|in:L,P',
            'target_tahfidz' => 'required|string|max:100',
            'level_tahfidz' => 'nullable|string|max:100',
            'metode' => 'nullable|string|max:100',
        ];
    }

    /**
     * Configure the validator instance to prevent enrollment for unowned children.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $studentId = $this->input('student_id');
            if ($studentId && $studentId !== 'new') {
                $parent = $this->user()?->parentProfile;
                if (! $parent || ! $parent->students()->where('students.id', $studentId)->exists()) {
                    $validator->errors()->add('student_id', 'Santri tidak terdaftar pada akun orang tua Anda.');
                }
            }
        });
    }
}
