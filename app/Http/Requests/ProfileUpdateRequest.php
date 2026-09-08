<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$userId],
            'phone' => ['nullable', 'string', 'max:20'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address' => ['nullable', 'string', 'max:500'],
            'maps_link' => ['nullable', 'url', 'max:500'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:L,P,male,female'],
            'city' => ['nullable', 'string', 'max:100'],
            'education' => ['nullable', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:150'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'hifz_total_juz' => ['nullable', 'integer', 'min:0', 'max:30'],
            'sanad_chain' => ['nullable', 'string', 'max:1000'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_holder' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini telah digunakan oleh akun lain.',
            'avatar.max' => 'Ukuran file foto maksimal adalah 2MB.',
            'avatar.image' => 'Berkas harus berupa gambar yang valid (JPG, PNG, atau WEBP).',
            'avatar.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'maps_link.url' => 'Format tautan peta tidak valid. Pastikan diawali dengan http:// atau https://',
            'cv.mimes' => 'Berkas CV wajib berformat PDF.',
            'cv.max' => 'Ukuran berkas CV maksimal adalah 2MB.',
            'certificate.max' => 'Ukuran berkas sertifikat maksimal adalah 2MB.',
            'hifz_total_juz.max' => 'Jumlah hafalan Al-Qur\'an maksimal adalah 30 Juz.',
        ];
    }
}
