<?php

use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create([
        'email' => 'admin@alhikmah.com',
    ]);

    $this->parent = User::factory()->parent()->create([
        'email' => 'parent@alhikmah.com',
    ]);
});

test('non admin cannot access gallery categories management', function () {
    $response = $this->actingAs($this->parent)->get(route('admin.gallery-categories.index'));
    $response->assertStatus(403);
});

test('admin can view gallery categories management list with stats', function () {
    $cat = GalleryCategory::factory()->create([
        'name' => 'Kegiatan Tahsin Akbar',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.gallery-categories.index'));

    $response->assertStatus(200);
    $response->assertSee('Kategori Galeri Kegiatan');
    $response->assertSee('Kegiatan Tahsin Akbar');
});

test('admin can create new gallery category with valid data', function () {
    $payload = [
        'name' => 'Kajian Spesial Ramadhan',
        'slug' => 'kajian_spesial_ramadhan',
        'group' => 'Acara Khusus',
        'icon' => 'bi-balloon',
        'badge_class' => 'bg-warning text-dark',
        'description' => 'Dokumentasi kegiatan pesantren kilat dan tadarus akbar di bulan suci.',
        'is_active' => '1',
        'sort_order' => 5,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.gallery-categories.store'), $payload);

    $response->assertRedirect(route('admin.gallery-categories.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('gallery_categories', [
        'name' => 'Kajian Spesial Ramadhan',
        'slug' => 'kajian_spesial_ramadhan',
        'group' => 'Acara Khusus',
        'is_active' => true,
    ]);
});

test('admin can update existing gallery category', function () {
    $cat = GalleryCategory::factory()->create([
        'name' => 'Nama Lama',
        'slug' => 'nama_lama',
    ]);

    $payload = [
        'name' => 'Nama Baru Diperbarui',
        'slug' => 'nama_baru_diperbarui',
        'group' => 'Kategori Utama',
        'icon' => 'bi-book',
        'badge_class' => 'bg-success',
        'description' => 'Deskripsi baru.',
        'is_active' => '1',
        'sort_order' => 2,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.gallery-categories.update', $cat->id), $payload);

    $response->assertRedirect(route('admin.gallery-categories.index'));
    $this->assertDatabaseHas('gallery_categories', [
        'id' => $cat->id,
        'name' => 'Nama Baru Diperbarui',
        'slug' => 'nama_baru_diperbarui',
    ]);
});

test('admin can toggle category active status', function () {
    $cat = GalleryCategory::factory()->create(['is_active' => true]);

    $response = $this->actingAs($this->admin)->post(route('admin.gallery-categories.toggle', $cat->id));

    $response->assertSessionHas('success');
    expect($cat->fresh()->is_active)->toBeFalse();

    // Toggle kembali ke true
    $this->actingAs($this->admin)->post(route('admin.gallery-categories.toggle', $cat->id));
    expect($cat->fresh()->is_active)->toBeTrue();
});

test('admin can reorder categories atomically', function () {
    $cat1 = GalleryCategory::factory()->create(['sort_order' => 1]);
    $cat2 = GalleryCategory::factory()->create(['sort_order' => 2]);

    $payload = [
        'items' => [
            ['id' => $cat1->id, 'sort_order' => 10],
            ['id' => $cat2->id, 'sort_order' => 5],
        ],
    ];

    $response = $this->actingAs($this->admin)->postJson(route('admin.gallery-categories.reorder'), $payload);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    expect($cat1->fresh()->sort_order)->toBe(10);
    expect($cat2->fresh()->sort_order)->toBe(5);
});

test('admin can delete category and safely detach linked galleries', function () {
    $cat = GalleryCategory::factory()->create(['name' => 'Kategori Hapus']);

    $gallery = Gallery::create([
        'title' => 'Foto Dokumentasi Kegiatan',
        'category' => $cat->slug,
        'category_id' => $cat->id,
        'image_url' => 'assets/img/1.jpg',
        'is_published' => true,
        'uploaded_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->delete(route('admin.gallery-categories.destroy', $cat->id));

    $response->assertRedirect(route('admin.gallery-categories.index'));
    $this->assertSoftDeleted('gallery_categories', ['id' => $cat->id]);

    // Verifikasi relasi galeri tetap utuh dan category_id terlepas dengan aman (null)
    expect($gallery->fresh()->category_id)->toBeNull();
});

test('admin can view trashed gallery categories', function () {
    $cat = GalleryCategory::factory()->create(['name' => 'Kategori Di Tong Sampah']);
    $cat->delete();

    $response = $this->actingAs($this->admin)->get(route('admin.gallery-categories.index', ['status' => 'trashed']));

    $response->assertStatus(200);
    $response->assertSee('Tong Sampah Kategori Galeri');
    $response->assertSee('Kategori Di Tong Sampah');
});

test('admin can restore trashed gallery category', function () {
    $cat = GalleryCategory::factory()->create(['name' => 'Kategori Pulih']);
    $cat->delete();

    expect($cat->fresh()->trashed())->toBeTrue();

    $response = $this->actingAs($this->admin)->post(route('admin.gallery-categories.restore', $cat->id));

    $response->assertRedirect(route('admin.gallery-categories.index', ['status' => 'trashed']));
    $response->assertSessionHas('success');

    expect($cat->fresh()->trashed())->toBeFalse();
});

test('admin can permanently force delete gallery category', function () {
    $cat = GalleryCategory::factory()->create(['name' => 'Kategori Musnah']);
    $cat->delete();

    $response = $this->actingAs($this->admin)->delete(route('admin.gallery-categories.force-delete', $cat->id));

    $response->assertRedirect(route('admin.gallery-categories.index', ['status' => 'trashed']));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('gallery_categories', ['id' => $cat->id]);
});

test('public gallery loads active categories from database', function () {
    $catActive = GalleryCategory::factory()->create([
        'name' => 'Bimbingan Spesial Tahsin',
        'slug' => 'bimbingan_spesial_tahsin',
        'is_active' => true,
    ]);

    $catInactive = GalleryCategory::factory()->create([
        'name' => 'Kategori Tersembunyi',
        'slug' => 'kategori_tersembunyi',
        'is_active' => false,
    ]);

    $response = $this->get(route('galeri'));

    $response->assertStatus(200);
    $response->assertSee('Bimbingan Spesial Tahsin');
    $response->assertDontSee('Kategori Tersembunyi');
});
