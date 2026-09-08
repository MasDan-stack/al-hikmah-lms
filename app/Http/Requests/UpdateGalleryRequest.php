<?php

namespace App\Http\Requests;

use App\Models\GalleryCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tags') && is_string($this->tags)) {
            $tagsArray = array_values(array_filter(array_map('trim', explode(',', $this->tags))));
            $this->merge(['tags' => $tagsArray]);
        }

        if ($this->filled('category_id') && ! $this->filled('category')) {
            $category = GalleryCategory::find($this->category_id);
            if ($category) {
                $this->merge(['category' => $category->slug]);
            }
        } elseif ($this->filled('category') && ! $this->filled('category_id')) {
            $catId = GalleryCategory::where('slug', $this->category)->value('id');
            if ($catId) {
                $this->merge(['category_id' => $catId]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:gallery_categories,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg,bmp,gif', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:150'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
