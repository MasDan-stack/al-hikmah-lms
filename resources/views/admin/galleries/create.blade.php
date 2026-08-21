@extends('layouts.admin')

@section('title', 'Tambah Dokumentasi Galeri')
@section('header', 'Tambah Foto & Dokumentasi Baru')
@section('subheader', 'Unggah foto kegiatan belajar dan lengkapi rincian informasi untuk publikasi.')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-3">
        <a href="{{ route('admin.galleries.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Galeri Kegiatan
        </a>
    </div>

    <!-- Flash Alert Notification Messages -->
    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div class="fw-semibold">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                <span class="fw-bold">Gagal Menyimpan Dokumentasi! Periksa kesalahan input berikut:</span>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <!-- Kolom Kiri: Input Data Utama -->
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Judul Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control rounded-3 @error('title') is-invalid @enderror" placeholder="Contoh: Bimbingan Tahsin Tartil Santri Anak" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-bold">Kategori Kegiatan <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" class="form-select rounded-3 @error('category_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->group }} &raquo; {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">Kategori foto</small>
                                    <a href="{{ route('admin.gallery-categories.index') }}" target="_blank" class="small text-decoration-none text-success">
                                        <i class="bi bi-plus-circle me-1"></i> Kelola Kategori
                                    </a>
                                </div>
                                @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="program_id" class="form-label fw-bold">Program Terkait (Opsional)</label>
                                <select name="program_id" id="program_id" class="form-select rounded-3 @error('program_id') is-invalid @enderror">
                                    <option value="">-- Tidak Terikat Program Khusus --</option>
                                    @foreach ($programs as $prog)
                                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                                    @endforeach
                                </select>
                                @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="caption" class="form-label fw-bold">Takarir Ringkas (Caption) <span class="text-muted fw-normal">(Tampil di kartu foto)</span></label>
                            <input type="text" name="caption" id="caption" class="form-control rounded-3 @error('caption') is-invalid @enderror" placeholder="Satu kalimat ringkas menggambarkan suasana foto..." value="{{ old('caption') }}">
                            @error('caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Penjelasan Rinci Kegiatan <span class="text-muted fw-normal">(Tampil saat foto di-klik)</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control rounded-3 @error('description') is-invalid @enderror" placeholder="Ceritakan alur kegiatan, suasana belajar, capaian santri, atau kutipan bimbingan...">{!! old('description') !!}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Kolom Kanan: Upload Gambar & Meta Tag -->
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Unggah Berkategori Gambar <span class="text-danger">*</span></label>
                            <div class="card border-dashed p-3 text-center bg-light rounded-4">
                                <img id="imagePreview" src="https://placehold.co/800x600/0d7a3e/ffffff?text=Pratinjau+Gambar" class="img-fluid rounded-3 mb-3 object-fit-cover shadow-sm" style="max-height: 220px; width: 100%;">
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp" onchange="previewFileImage(this)" required>
                                <small class="text-muted d-block mt-2">Format: JPG, PNG, WEBP. Maksimal: 3 MB.</small>
                            </div>
                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="event_date" class="form-label fw-bold">Tanggal Kegiatan</label>
                                <input type="date" name="event_date" id="event_date" class="form-control rounded-3" value="{{ old('event_date', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-bold">Lokasi Pelaksanaan</label>
                                <input type="text" name="location" id="location" class="form-control rounded-3" placeholder="Contoh: Jakarta / Zoom" value="{{ old('location') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tags" class="form-label fw-bold">Tag Kata Kunci <span class="text-muted fw-normal">(Pisahkan dengan koma)</span></label>
                            <input type="text" name="tags" id="tags" class="form-control rounded-3" placeholder="Tahsin, Anak, Offline" value="{{ is_array(old('tags')) ? implode(', ', old('tags')) : old('tags') }}">
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">Rekomendasi Tag Quick-Add:</small>
                                @foreach ($defaultTags as $t)
                                    <button type="button" class="btn btn-sm btn-light border rounded-pill py-0 px-2 mb-1 me-1" onclick="addTagToInput('{{ $t }}')">+ {{ $t }}</button>
                                @endforeach
                            </div>
                        </div>

                        <div class="card bg-light border-0 p-3 rounded-4 mb-3">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1" {{ old('is_published', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_published">Publikasikan Langsung ke Website</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold text-warning" for="is_featured">
                                    <i class="bi bi-star-fill me-1"></i> Tampilkan di Hero Slideshow Utama
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom rounded-pill w-100 py-2">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Simpan Dokumentasi
                            </button>
                            <a href="{{ route('admin.galleries.index') }}" class="btn btn-light border rounded-pill px-4 py-2">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewFileImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addTagToInput(tag) {
        const input = document.getElementById('tags');
        let current = input.value.split(',').map(s => s.trim()).filter(s => s.length > 0);
        if (!current.includes(tag)) {
            current.push(tag);
            input.value = current.join(', ');
        }
    }
</script>
@endpush
@endsection
