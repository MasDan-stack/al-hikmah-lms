<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $categories = array_keys(Gallery::CATEGORIES);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title).'-'.Str::random(5),
            'category' => fake()->randomElement($categories),
            'program_id' => Program::inRandomOrder()->value('id'),
            'image_url' => 'https://placehold.co/800x600/0d7a3e/ffffff?text=Momen+'.fake()->numberBetween(1, 10),
            'caption' => fake()->sentence(6),
            'description' => fake()->paragraphs(2, true),
            'event_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'location' => fake()->randomElement(['Ruang Belajar Utama AL-HIKMAH', 'Home Visit Jakarta Timur', 'Online Zoom Room', 'Masjid Al-Hikmah']),
            'tags' => fake()->randomElements(Gallery::DEFAULT_TAGS, fake()->numberBetween(1, 3)),
            'is_featured' => fake()->boolean(25),
            'is_published' => true,
            'sort_order' => 0,
            'views_count' => fake()->numberBetween(10, 500),
            'uploaded_by' => User::first()?->id ?? User::factory(),
        ];
    }
}
