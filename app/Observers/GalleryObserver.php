<?php

namespace App\Observers;

use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryObserver
{
    /**
     * Tangani event updating: Hapus berkas fisik lama jika gambar diganti
     */
    public function updating(Gallery $gallery): void
    {
        if ($gallery->isDirty('image_url')) {
            $originalImage = $gallery->getOriginal('image_url');
            if ($originalImage && ! Str::startsWith($originalImage, ['http', 'assets/'])) {
                Storage::disk('public')->delete($originalImage);
            }
        }
    }

    /**
     * Tangani event forceDeleted: Hapus berkas fisik saat data dihapus permanen
     */
    public function forceDeleted(Gallery $gallery): void
    {
        if ($gallery->image_url && ! Str::startsWith($gallery->image_url, ['http', 'assets/'])) {
            Storage::disk('public')->delete($gallery->image_url);
        }
    }
}
