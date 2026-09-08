<?php

namespace Database\Factories;

use App\Models\GalleryCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GalleryCategory>
 */
class GalleryCategoryFactory extends Factory
{
    protected $model = GalleryCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'group' => fake()->randomElement(array_keys(GalleryCategory::GROUPS)),
            'icon' => fake()->randomElement(array_keys(GalleryCategory::ICON_OPTIONS)),
            'badge_class' => fake()->randomElement(array_keys(GalleryCategory::BADGE_OPTIONS)),
            'description' => fake()->sentence(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
