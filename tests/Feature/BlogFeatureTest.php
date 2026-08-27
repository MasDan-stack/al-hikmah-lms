<?php

use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
    $this->adminUser = User::factory()->create(['role_id' => $this->adminRole->id]);
});

test('admin can access blog dashboard and list articles', function () {
    $this->actingAs($this->adminUser)
        ->get(route('admin.blog.index'))
        ->assertStatus(200)
        ->assertSee('Daftar Artikel Blog');
});

test('admin can create a new article with tags and cover image', function () {
    Storage::fake('public');
    $category = BlogCategory::create([
        'name' => 'Tahsin Balita',
        'slug' => 'tahsin-balita',
    ]);
    $tag = BlogTag::create([
        'name' => 'Tips Mengaji',
        'slug' => 'tips-mengaji',
    ]);
    $file = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');

    $payload = [
        'title' => 'Panduan Memulai Belajar Iqra untuk Balita',
        'category_id' => $category->id,
        'excerpt' => 'Tips mendampingi balita belajar huruf hijaiyah di rumah.',
        'content' => '<p>Langkah pertama belajar Iqra dengan penuh keceriaan dan kesabaran.</p>',
        'status' => 'published',
        'cover_image' => $file,
        'tags' => [$tag->id],
    ];

    $this->actingAs($this->adminUser)
        ->post(route('admin.blog.store'), $payload)
        ->assertRedirect(route('admin.blog.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('articles', [
        'title' => 'Panduan Memulai Belajar Iqra untuk Balita',
    ]);
});

test('admin can soft delete and restore article', function () {
    $article = Article::create([
        'user_id' => $this->adminUser->id,
        'title' => 'Artikel Uji Coba Hapus',
        'slug' => 'artikel-uji-coba-hapus',
        'content' => '<p>Konten artikel uji coba hapus</p>',
        'status' => 'published',
    ]);

    // 1. Soft Delete
    $this->actingAs($this->adminUser)
        ->delete(route('admin.blog.destroy', $article->id))
        ->assertRedirect(route('admin.blog.index'));

    $this->assertSoftDeleted('articles', ['id' => $article->id]);

    // 2. Trash list
    $this->actingAs($this->adminUser)
        ->get(route('admin.blog.trash'))
        ->assertStatus(200)
        ->assertSee('Artikel Uji Coba Hapus');

    // 3. Restore
    $this->actingAs($this->adminUser)
        ->post(route('admin.blog.restore', $article->id))
        ->assertRedirect(route('admin.blog.trash'));

    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'deleted_at' => null,
    ]);
});

test('public visitors can view published articles and sitemap xml', function () {
    $published = Article::create([
        'user_id' => $this->adminUser->id,
        'title' => 'Artikel Tayang Publik',
        'slug' => 'artikel-tayang-publik',
        'content' => '<p>Isi artikel tayang publik</p>',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $draft = Article::create([
        'user_id' => $this->adminUser->id,
        'title' => 'Artikel Konsep Draft',
        'slug' => 'artikel-konsep-draft',
        'content' => '<p>Isi artikel konsep draft</p>',
        'status' => 'draft',
    ]);

    // Catalog page
    $this->get(route('blog.index'))
        ->assertStatus(200)
        ->assertSee($published->title)
        ->assertDontSee($draft->title);

    // Sitemap XML endpoint
    $response = $this->get(route('sitemap'));
    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'text/xml; charset=UTF-8')
        ->assertSee($published->slug);
});

test('public visitors can filter articles by category and tag', function () {
    $category = BlogCategory::create(['name' => 'Tahsin Quran', 'slug' => 'tahsin-quran']);
    $tag = BlogTag::create(['name' => 'Tips Mengaji', 'slug' => 'tips-mengaji']);

    $article = Article::create([
        'user_id' => $this->adminUser->id,
        'category_id' => $category->id,
        'title' => 'Artikel Filter Kategori Tag',
        'slug' => 'artikel-filter-kategori-tag',
        'content' => '<p>Isi artikel filter</p>',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);
    $article->tags()->attach($tag->id);

    $this->get(route('blog.category', $category->slug))
        ->assertStatus(200)
        ->assertSee($article->title);

    $this->get(route('blog.tag', $tag->slug))
        ->assertStatus(200)
        ->assertSee($article->title);
});

test('reading an article increments view count and sharing increments share count', function () {
    $article = Article::create([
        'user_id' => $this->adminUser->id,
        'title' => 'Artikel Pelacak Counter',
        'slug' => 'artikel-pelacak-counter',
        'content' => '<p>Isi artikel pelacak counter</p>',
        'status' => 'published',
        'published_at' => now()->subDay(),
        'views_count' => 0,
        'shares_count' => 0,
    ]);

    // View counter
    $this->get(route('blog.show', $article->slug))->assertStatus(200);
    expect($article->fresh()->views_count)->toBe(1);

    // Track share endpoint
    $this->post(route('blog.share', $article->slug))
        ->assertStatus(200)
        ->assertJson(['status' => 'success']);
    expect($article->fresh()->shares_count)->toBe(1);
});

test('admin can upload inline image for ckeditor', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('content-figure.png', 100, 'image/png');

    $response = $this->actingAs($this->adminUser)
        ->post(route('admin.blog.upload-image'), [
            'upload' => $file,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['url', 'default']);

    Storage::disk('public')->assertExists('blog/content/'.$file->hashName());
});
