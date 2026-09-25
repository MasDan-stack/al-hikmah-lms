@extends('layouts.admin')

@section('title', 'Tulis Artikel Baru | Admin AL-HIKMAH')

@push('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 400px;
        max-height: 700px;
        font-size: 1rem;
        line-height: 1.75;
        font-family: inherit;
    }
    .ck-content blockquote {
        border-left: 4px solid #0d7a3e;
        padding-left: 1rem;
        font-style: italic;
        color: #4b5563;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1 text-heading">
                <i class="bi bi-pencil-square text-success me-2"></i>Tulis Artikel Baru
            </h3>
            <p class="text-muted small mb-0">Buat publikasi materi edukasi Al-Qur'an, parenting, dan kabar lembaga.</p>
        </div>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

    <!-- Form Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- Column Left: Main Fields -->
                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Judul Artikel <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg rounded-3 @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="Contoh: 5 Tips Mendampingi Balita Belajar Iqra di Rumah" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="excerpt" class="form-label fw-bold">Ringkasan Singkat (Excerpt)</label>
                            <textarea name="excerpt" id="excerpt" rows="2" class="form-control rounded-3" 
                                      placeholder="Ringkasan 1-2 kalimat yang tampil pada kartu artikel...">{{ old('excerpt') }}</textarea>
                            <div class="form-text">Jika dikosongkan, ringkasan akan otomatis diambil dari paragraf pertama konten.</div>
                        </div>

                        <div class="mb-3">
                            <label for="editor" class="form-label fw-bold">Konten Lengkap Artikel <span class="text-danger">*</span></label>
                            <textarea name="content" id="editor" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Column Right: Metadata & Cover -->
                    <div class="col-lg-4">
                        <!-- Cover Image Card -->
                        <div class="card border border-secondary-subtle rounded-4 p-3 mb-4 bg-light-subtle">
                            <h6 class="fw-bold mb-3"><i class="bi bi-image text-success me-2"></i>Gambar Sampul (Cover)</h6>
                            <div class="mb-3 text-center">
                                <img id="coverPreview" src="{{ asset('assets/img/logo/logo.png') }}" 
                                     alt="Preview Cover" class="img-fluid rounded-3 mb-2 shadow-sm border" style="max-height: 180px; object-fit: cover;">
                            </div>
                            <div class="mb-2">
                                <input type="file" name="cover_image" id="cover_image" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this)">
                                <div class="form-text">Format: JPG, PNG, WEBP. Maks 2MB.</div>
                            </div>
                            <div>
                                <label for="cover_caption" class="form-label small text-muted mb-1">Keterangan Gambar (Caption)</label>
                                <input type="text" name="cover_caption" id="cover_caption" class="form-control form-control-sm rounded-2" 
                                       value="{{ old('cover_caption') }}" placeholder="Keterangan gambar sampul...">
                            </div>
                        </div>

                        <!-- Publishing Options -->
                        <div class="card border border-secondary-subtle rounded-4 p-3 mb-4 bg-light-subtle">
                            <h6 class="fw-bold mb-3"><i class="bi bi-gear text-success me-2"></i>Pengaturan Publikasi</h6>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold small">Status Publikasi <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (Langsung Tayang)</option>
                                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (Simpan Konsep)</option>
                                    <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>Scheduled (Terjadwal)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="published_at" class="form-label fw-bold small">Tanggal Rilis Publikasi</label>
                                <input type="datetime-local" name="published_at" id="published_at" class="form-control form-control-sm" value="{{ old('published_at') }}">
                                <div class="form-text">Kosongkan jika ingin menggunakan waktu saat ini.</div>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-input-label fw-bold small" for="is_featured">
                                    Jadikan Artikel Unggulan (Featured)
                                </label>
                            </div>
                        </div>

                        <!-- Category & Tags -->
                        <div class="card border border-secondary-subtle rounded-4 p-3 bg-light-subtle">
                            <h6 class="fw-bold mb-3"><i class="bi bi-tags text-success me-2"></i>Kategori & Tags</h6>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-bold small">Kategori Utama</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small d-block">Tags Taksonomi</label>
                                <div class="row g-2 border rounded-3 p-2 bg-white" style="max-height: 180px; overflow-y: auto;">
                                    @forelse($tags as $tag)
                                        <div class="col-6">
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                                       {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="tag_{{ $tag->id }}">#{{ $tag->name }}</label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-muted small text-center py-2">Belum ada tag. <a href="{{ route('admin.blog.tags.index') }}" target="_blank">Tambah Tag</a></div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.blog.index') }}" class="btn btn-secondary rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="bi bi-save me-1"></i>Simpan Artikel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    // CKEditor Custom Upload Adapter
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
            this.url = "{{ route('admin.blog.upload-image') }}";
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    this._initRequest();
                    this._initListeners(resolve, reject, file);
                    this._sendRequest(file);
                }));
        }

        abort() {
            if (this.xhr) {
                this.xhr.abort();
            }
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();
            xhr.open('POST', this.url, true);
            xhr.responseType = 'json';

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
            }
            xhr.setRequestHeader('Accept', 'application/json');
        }

        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `Gagal mengunggah berkas: ${file.name}.`;

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;

                if (!response || xhr.status >= 400) {
                    return reject(response && response.message ? response.message : genericErrorText);
                }

                resolve({
                    default: response.url || response.default
                });
            });

            if (xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if (evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        _sendRequest(file) {
            const data = new FormData();
            data.append('upload', file);
            this.xhr.send(data);
        }
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        const editorElement = document.querySelector('#editor');
        if (editorElement) {
            ClassicEditor
                .create(editorElement, {
                    extraPlugins: [MyCustomUploadAdapterPlugin],
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'link', 'uploadImage', 'blockQuote', 'insertTable', '|',
                            'bulletedList', 'numberedList', 'horizontalLine', '|',
                            'undo', 'redo'
                        ]
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraf Normal', class: 'ck-heading_paragraph' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2 (Sub-Judul Utama)', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3 (Bagian Kecil)', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4 (Sub-Poin)', class: 'ck-heading_heading4' }
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .catch(error => {
                    console.error('CKEditor Initialization Error:', error);
                });
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('coverPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
