<?php

use App\Models\Gallery;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');

    $this->admin = User::factory()->admin()->create([
        'email' => 'admin@alhikmah.com',
    ]);

    $this->parent = User::factory()->parent()->create([
        'email' => 'parent@alhikmah.com',
    ]);

    $this->program = Program::factory()->create([
        'name' => 'Tahsin Utama',
        'is_active' => true,
    ]);
});

test('public user can view gallery page and active items', function () {
    Gallery::factory()->create([
        'title' => 'Momen Belajar Tartil',
        'is_published' => true,
        'program_id' => $this->program->id,
    ]);

    $response = $this->get(route('galeri'));

    $response->assertStatus(200)
        ->assertSee('Momen Belajar Tartil');
});

test('non admin cannot access admin gallery management', function () {
    $response = $this->actingAs($this->parent)
        ->get(route('admin.galleries.index'));

    $response->assertStatus(403);
});

test('admin can view admin gallery management list', function () {
    Gallery::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)
        ->get(route('admin.galleries.index'));

    $response->assertStatus(200)
        ->assertSee('Galeri & Dokumentasi Kegiatan');
});

test('admin can upload new gallery item with tags and file', function () {
    $file = UploadedFile::fake()->create('kegiatan.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($this->admin)
        ->post(route('admin.galleries.store'), [
            'title' => 'Ujian Hafalan Juz 30',
            'category' => 'kegiatan_santri',
            'program_id' => $this->program->id,
            'image' => $file,
            'caption' => 'Ujian santri juz 30',
            'description' => 'Penjelasan lengkap ujian hafalan juz 30.',
            'event_date' => '2026-08-20',
            'location' => 'Jakarta',
            'tags' => 'Anak, Tahfidz, Offline',
            'is_published' => '1',
            'is_featured' => '1',
        ]);

    $response->assertRedirect(route('admin.galleries.index'))
        ->assertSessionHas('success');

    $gallery = Gallery::where('title', 'Ujian Hafalan Juz 30')->first();
    expect($gallery)->not->toBeNull();
    expect($gallery->is_published)->toBeTrue();
    expect($gallery->is_featured)->toBeTrue();
    expect($gallery->tags)->toBe(['Anak', 'Tahfidz', 'Offline']);

    Storage::disk('public')->assertExists($gallery->image_url);
});

test('admin can soft delete gallery item move to trash and restore it', function () {
    $gallery = Gallery::factory()->create([
        'title' => 'Foto Mau Dihapus',
        'is_published' => true,
    ]);

    // Soft delete
    $deleteRes = $this->actingAs($this->admin)
        ->delete(route('admin.galleries.destroy', $gallery->id));

    $deleteRes->assertRedirect(route('admin.galleries.index'));
    $this->assertSoftDeleted('galleries', ['id' => $gallery->id]);

    // Restore
    $restoreRes = $this->actingAs($this->admin)
        ->post(route('admin.galleries.restore', $gallery->id));

    $restoreRes->assertRedirect(route('admin.galleries.index', ['status' => 'trashed']));
    $this->assertDatabaseHas('galleries', [
        'id' => $gallery->id,
        'deleted_at' => null,
    ]);
});

test('admin force delete permanently removes record and observer deletes storage file', function () {
    $filePath = 'galleries/test_delete.jpg';
    Storage::disk('public')->put($filePath, 'fake content');

    $gallery = Gallery::factory()->create([
        'title' => 'Hapus Permanen',
        'image_url' => $filePath,
    ]);
    $gallery->delete(); // Move to trash first

    $forceRes = $this->actingAs($this->admin)
        ->delete(route('admin.galleries.force-delete', $gallery->id));

    $forceRes->assertRedirect(route('admin.galleries.index', ['status' => 'trashed']));
    $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    Storage::disk('public')->assertMissing($filePath);
});

test('view counter increments once per session anti spam', function () {
    $gallery = Gallery::factory()->create(['views_count' => 5]);

    // First view increment
    $res1 = $this->postJson(route('galeri.view', $gallery->id));
    $res1->assertJson(['success' => true, 'incremented' => true]);
    expect($gallery->fresh()->views_count)->toBe(6);

    // Second view in same session should NOT increment
    $res2 = $this->postJson(route('galeri.view', $gallery->id));
    $res2->assertJson(['success' => true, 'incremented' => false]);
    expect($gallery->fresh()->views_count)->toBe(6);
});

test('admin can reorder galleries atomically', function () {
    $g1 = Gallery::factory()->create(['sort_order' => 1]);
    $g2 = Gallery::factory()->create(['sort_order' => 2]);

    $reorderRes = $this->actingAs($this->admin)
        ->postJson(route('admin.galleries.reorder'), [
            'order' => [$g2->id, $g1->id],
        ]);

    $reorderRes->assertJson(['success' => true]);
    expect($g2->fresh()->sort_order)->toBe(1);
    expect($g1->fresh()->sort_order)->toBe(2);
});
