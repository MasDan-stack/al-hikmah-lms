@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold"><i class="bi bi-gear-fill text-success me-2"></i>Pengaturan Website</h1>
            <p class="text-muted small mb-0">Kelola kontak, nomor WhatsApp, sosial media, dan informasi umum website AL-HIKMAH.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            <!-- Informasi Kontak & WhatsApp -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 fw-bold text-success fs-5">
                        <i class="bi bi-telephone-fill me-2"></i>Kontak & Customer Service
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Nomor WhatsApp CS <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-whatsapp text-success"></i></span>
                                <input type="text" class="form-control border-start-0" name="settings[whatsapp_number]" 
                                       value="{{ site_setting('whatsapp_number', '6285786689008') }}" required placeholder="Contoh: 6285786689008">
                            </div>
                            <small class="text-muted">Gunakan format internasional tanpa spasi (misal: 6285786689008).</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Email Resmi Kontak</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                                <input type="email" class="form-control border-start-0" name="settings[email_contact]" 
                                       value="{{ site_setting('email_contact', 'belajarquranalhikmah@gmail.com') }}" placeholder="email@domain.com">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Username Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-instagram text-danger"></i></span>
                                <input type="text" class="form-control border-start-0" name="settings[instagram_handle]" 
                                       value="{{ site_setting('instagram_handle', 'houseofalhikmah') }}" placeholder="houseofalhikmah">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Alamat / Area Layanan</label>
                            <textarea class="form-control" name="settings[office_address]" rows="2" 
                                      placeholder="Jabodetabek & Sekitarnya">{{ site_setting('office_address', 'Jabodetabek & Sekitarnya (Home Visit & Online)') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Umum Lembaga -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 fw-bold text-success fs-5">
                        <i class="bi bi-building me-2"></i>Informasi Lembaga & Pendaftaran
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Nama Lembaga / Brand</label>
                            <input type="text" class="form-control" name="settings[site_name]" 
                                   value="{{ site_setting('site_name', 'AL-HIKMAH') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Tagline Website</label>
                            <input type="text" class="form-control" name="settings[site_tagline]" 
                                   value="{{ site_setting('site_tagline', 'Menemani Generasi Qur\'ani Indonesia') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary small">Biaya Pendaftaran Standar (Rp)</label>
                            <input type="number" class="form-control" name="settings[registration_fee]" 
                                   value="{{ site_setting('registration_fee', '150000') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary-custom px-5 py-3 rounded-3 fw-bold fs-6">
                <i class="bi bi-save me-2"></i> Simpan Perubahan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection
