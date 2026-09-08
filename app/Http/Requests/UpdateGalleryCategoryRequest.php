<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateGalleryCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name') && is_string($this->name)) {
            $this->merge([
                'slug' => $this->filled('slug') ? Str::slug($this->slug, '_') : Str::slug($this->name, '_'),
            ]);
        }
    }

    public function rules(): array
    {
        $categoryId = $this->route('gallery_category') ?? $this->route('id') ?? $this->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', Rule::unique('gallery_categories', 'slug')->ignore($categoryId)],
            'group' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:50'],
            'badge_class' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
