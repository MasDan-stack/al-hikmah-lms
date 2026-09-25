@extends('layouts.mentor')

@section('title', 'Catat Progres Bimbingan Santri')
@section('header', 'Catat Progres Belajar Santri')
@section('subheader', 'Input capaian surah, ayat, tajwid, kelancaran, dan adab harian santri binaan')

@section('content')
<div class="container-fluid p-0">
    <!-- Breadcrumb & Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('mentor.dashboard') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-xs">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
            <span class="text-muted small">/</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-monospace">
                Formulir Progres Santri
            </span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('mentor.progress.bulk-create') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold shadow-xs">
                <i class="bi bi-collection-fill me-1"></i> Mode Input Massal (Bulk)
            </a>
        </div>
    </div>

    <!-- Flash Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3.5" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-success p-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px;">
                    <i class="bi bi-check2-circle fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-success">Berhasil Tersimpan!</h6>
                    <span class="small">{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center justify-content-between p-3.5" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-white text-danger p-2 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px;">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-danger">Gagal Menyimpan Data</h6>
                    <span class="small">{{ session('error') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
                <span class="fw-bold text-danger">Terdapat Kesalahan Pengisian:</span>
            </div>
            <ul class="mb-0 ps-3 small text-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-9 col-lg-10">
            <form action="{{ route('mentor.progress.store') }}" method="POST" id="formProgressSantri">
                @csrf

                <!-- CARD 1: Santri & Sesi Bimbingan -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light-subtle pt-4 px-4 pb-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-badge-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">1. Data Santri &amp; Sesi Terkait</h6>
                                <small class="text-muted">Pilih santri binaan yang baru saja menyelesaikan materi pembelajaran</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Santri -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">
                                    Pilih Santri Binaan <span class="text-danger">*</span>
                                </label>
                                <select name="student_id" id="selectStudent" class="form-select @error('student_id') is-invalid @enderror" required onchange="filterSessionsByStudent(this.value)">
                                    <option value="">-- Pilih Nama Santri --</option>
                                    @foreach($students as $st)
                                        @php
                                            $prog = $st->programs->first()?->name ?? 'Program Al-Hikmah';
                                            $isSelected = (string)old('student_id', $selectedStudentId) === (string)$st->id;
                                        @endphp
                                        <option value="{{ $st->id }}" {{ $isSelected ? 'selected' : '' }} data-program="{{ $prog }}" data-parent="{{ $st->parent?->user?->name ?? 'Wali Santri' }}">
                                            {{ $st->user?->name ?? $st->full_name }} ({{ $prog }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted d-block mt-1" style="font-size: 0.73rem;">
                                    *Pemberitahuan laporan progres otomatis diteruskan ke portal wali santri.
                                </small>
                            </div>

                            <!-- Sesi Belajar -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small">
                                    Tautkan Sesi Belajar <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <select name="session_id" id="selectSession" class="form-select @error('session_id') is-invalid @enderror">
                                    <option value="">-- Tanpa Tautan Sesi / Bimbingan Mandiri --</option>
                                    @foreach($sessions as $sess)
                                        @php
                                            $sessDate = $sess->date ? \Carbon\Carbon::parse($sess->date)->locale('id')->isoFormat('ddd, D MMM Y') : '-';
                                            $sessTime = $sess->time ? date('H:i', strtotime($sess->time)) . ' WIB' : '';
                                            $stName = $sess->student?->user?->name ?? $sess->student?->full_name ?? 'Santri';
                                            $isSessSelected = (string)old('session_id', $selectedSessionId) === (string)$sess->id;
                                        @endphp
                                        <option value="{{ $sess->id }}" {{ $isSessSelected ? 'selected' : '' }} data-student-id="{{ $sess->student_id }}">
                                            {{ $sessDate }} ({{ $sessTime }}) - {{ $stName }} [{{ ucfirst($sess->status) }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error('session_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted d-block mt-1" style="font-size: 0.73rem;">
                                    *Menautkan sesi otomatis menandai sesi sebagai <strong>Selesai (Completed)</strong>.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Kategori & Capaian Materi Bimbingan -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light-subtle pt-4 px-4 pb-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-book-half fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">2. Materi &amp; Capaian Hafalan / Bacaan</h6>
                                <small class="text-muted">Tentukan jenis bimbingan serta rentang ayat atau jilid yang dipelajari</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Kategori Radio Pills -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small mb-2">
                                Kategori Bimbingan <span class="text-danger">*</span>
                            </label>
                            @php
                                $currentKategori = old('kategori', 'Tahfidz');
                            @endphp
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label class="kategori-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                           id="katCardTahfidz"
                                           style="cursor: pointer; {{ $currentKategori === 'Tahfidz' ? 'background: #ecfdf5; border-color: #059669 !important;' : 'background: #fafaf9;' }}">
                                        <input type="radio" name="kategori" value="Tahfidz" class="d-none" {{ $currentKategori === 'Tahfidz' ? 'checked' : '' }} onchange="selectKategoriUI('Tahfidz')">
                                        <i class="bi bi-bookmark-star-fill fs-3 text-success mb-1"></i>
                                        <span class="fw-bold text-dark small">Tahfidz</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">Setoran Hafalan Baru</span>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="kategori-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                           id="katCardTahsin"
                                           style="cursor: pointer; {{ $currentKategori === 'Tahsin' ? 'background: #eff6ff; border-color: #2563eb !important;' : 'background: #fafaf9;' }}">
                                        <input type="radio" name="kategori" value="Tahsin" class="d-none" {{ $currentKategori === 'Tahsin' ? 'checked' : '' }} onchange="selectKategoriUI('Tahsin')">
                                        <i class="bi bi-mic-fill fs-3 text-primary mb-1"></i>
                                        <span class="fw-bold text-dark small">Tahsin</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">Kaidah &amp; Tartil Bacaan</span>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="kategori-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                           id="katCardIqra"
                                           style="cursor: pointer; {{ $currentKategori === 'Iqra' ? 'background: #fffbeb; border-color: #d97706 !important;' : 'background: #fafaf9;' }}">
                                        <input type="radio" name="kategori" value="Iqra" class="d-none" {{ $currentKategori === 'Iqra' ? 'checked' : '' }} onchange="selectKategoriUI('Iqra')">
                                        <i class="bi bi-spellcheck fs-3 text-warning mb-1"></i>
                                        <span class="fw-bold text-dark small">Iqra &amp; Jilid</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">Pengenalan Huruf</span>
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="kategori-card p-3 rounded-3 border d-flex flex-column align-items-center text-center cursor-pointer h-100 transition-all" 
                                           id="katCardAdab"
                                           style="cursor: pointer; {{ $currentKategori === 'Adab' ? 'background: #fdf2f8; border-color: #db2777 !important;' : 'background: #fafaf9;' }}">
                                        <input type="radio" name="kategori" value="Adab" class="d-none" {{ $currentKategori === 'Adab' ? 'checked' : '' }} onchange="selectKategoriUI('Adab')">
                                        <i class="bi bi-heart-fill fs-3 text-danger mb-1"></i>
                                        <span class="fw-bold text-dark small">Adab &amp; Doa</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">Akhlak &amp; Doa Harian</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Surah & Ayat -->
                        <div class="row g-3">
                            <!-- Juz -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary small">
                                    Juz Al-Qur'an (1 - 30)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-hash"></i></span>
                                    <input type="number" 
                                           name="juz" 
                                           id="inputJuz"
                                           class="form-control border-start-0 ps-0 @error('juz') is-invalid @enderror" 
                                           min="1" 
                                           max="30" 
                                           placeholder="30" 
                                           value="{{ old('juz', 30) }}">
                                </div>
                                @error('juz') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <div class="d-flex gap-1 mt-1.5 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setJuz(30)">Juz 30</button>
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setJuz(29)">Juz 29</button>
                                    <button type="button" class="btn btn-xs btn-light border rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setJuz(1)">Juz 1</button>
                                </div>
                            </div>

                            <!-- Surah / Judul Materi -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-secondary small">
                                    Nama Surah / Materi / Jilid
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-journal-bookmark-fill"></i></span>
                                    <input type="text" 
                                           name="surah_start" 
                                           id="inputSurah"
                                           list="quranSurahList" 
                                           class="form-control border-start-0 ps-0 @error('surah_start') is-invalid @enderror" 
                                           placeholder="Ketik nama surah, misal: An-Naba..." 
                                           value="{{ old('surah_start') }}">
                                </div>
                                @error('surah_start') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <datalist id="quranSurahList">
                                    <option value="Al-Fatihah">1. Al-Fatihah</option>
                                    <option value="Al-Baqarah">2. Al-Baqarah</option>
                                    <option value="Ali 'Imran">3. Ali 'Imran</option>
                                    <option value="An-Nisa'">4. An-Nisa'</option>
                                    <option value="Al-Ma'idah">5. Al-Ma'idah</option>
                                    <option value="Yasin">36. Yasin</option>
                                    <option value="Al-Waqi'ah">56. Al-Waqi'ah</option>
                                    <option value="Al-Mulk">67. Al-Mulk</option>
                                    <option value="Al-Qalam">68. Al-Qalam</option>
                                    <option value="An-Naba'">78. An-Naba'</option>
                                    <option value="An-Nazi'at">79. An-Nazi'at</option>
                                    <option value="'Abasa">80. 'Abasa</option>
                                    <option value="At-Takwir">81. At-Takwir</option>
                                    <option value="Al-Infithar">82. Al-Infithar</option>
                                    <option value="Al-Muthaffifin">83. Al-Muthaffifin</option>
                                    <option value="Al-Insyiqaq">84. Al-Insyiqaq</option>
                                    <option value="Al-Buruj">85. Al-Buruj</option>
                                    <option value="Ath-Thariq">86. Ath-Thariq</option>
                                    <option value="Al-A'la">87. Al-A'la</option>
                                    <option value="Al-Ghasyiyah">88. Al-Ghasyiyah</option>
                                    <option value="Al-Fajr">89. Al-Fajr</option>
                                    <option value="Al-Balad">90. Al-Balad</option>
                                    <option value="Asy-Syams">91. Asy-Syams</option>
                                    <option value="Al-Lail">92. Al-Lail</option>
                                    <option value="Adh-Dhuha">93. Adh-Dhuha</option>
                                    <option value="Al-Insyirah">94. Al-Insyirah</option>
                                    <option value="At-Tin">95. At-Tin</option>
                                    <option value="Al-'Alaq">96. Al-'Alaq</option>
                                    <option value="Al-Qadr">97. Al-Qadr</option>
                                    <option value="Al-Bayyinah">98. Al-Bayyinah</option>
                                    <option value="Az-Zalzalah">99. Az-Zalzalah</option>
                                    <option value="Al-'Adiyat">100. Al-'Adiyat</option>
                                    <option value="Al-Qari'ah">101. Al-Qari'ah</option>
                                    <option value="At-Takatsur">102. At-Takatsur</option>
                                    <option value="Al-'Ashr">103. Al-'Ashr</option>
                                    <option value="Al-Humazah">104. Al-Humazah</option>
                                    <option value="Al-Fil">105. Al-Fil</option>
                                    <option value="Quraisy">106. Quraisy</option>
                                    <option value="Al-Ma'un">107. Al-Ma'un</option>
                                    <option value="Al-Kautsar">108. Al-Kautsar</option>
                                    <option value="Al-Kafirun">109. Al-Kafirun</option>
                                    <option value="An-Nashr">110. An-Nashr</option>
                                    <option value="Al-Lahab">111. Al-Lahab</option>
                                    <option value="Al-Ikhlas">112. Al-Ikhlas</option>
                                    <option value="Al-Falaq">113. Al-Falaq</option>
                                    <option value="An-Nas">114. An-Nas</option>
                                    <option value="Iqra Jilid 1">Iqra Jilid 1</option>
                                    <option value="Iqra Jilid 2">Iqra Jilid 2</option>
                                    <option value="Iqra Jilid 3">Iqra Jilid 3</option>
                                    <option value="Iqra Jilid 4">Iqra Jilid 4</option>
                                    <option value="Iqra Jilid 5">Iqra Jilid 5</option>
                                    <option value="Iqra Jilid 6">Iqra Jilid 6</option>
                                </datalist>
                            </div>

                            <!-- Rentang Ayat Mulai & Selesai -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary small">Ayat Mulai</label>
                                <input type="number" 
                                       name="ayat_start" 
                                       class="form-control @error('ayat_start') is-invalid @enderror" 
                                       placeholder="1" 
                                       value="{{ old('ayat_start') }}">
                                @error('ayat_start') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-secondary small">Ayat Selesai</label>
                                <input type="number" 
                                       name="ayat_end" 
                                       class="form-control @error('ayat_end') is-invalid @enderror" 
                                       placeholder="10" 
                                       value="{{ old('ayat_end') }}">
                                @error('ayat_end') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Penilaian Kualitas & Karakter Santri -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light-subtle pt-4 px-4 pb-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-star-half fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">3. Penilaian Kualitas Bacaan &amp; Sikap Santri</h6>
                                <small class="text-muted">Standar evaluasi Al-Hikmah mencakup tajwid makhraj, kelancaran, dan adab akhlak</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Nilai Tajwid -->
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-secondary small mb-0">
                                            <i class="bi bi-patch-check-fill text-success me-1"></i>Nilai Tajwid
                                        </label>
                                        <span class="badge bg-white text-dark border font-monospace" id="labelTajwid">{{ old('nilai_tajwid', 85) }}</span>
                                    </div>
                                    <input type="number" 
                                           name="nilai_tajwid" 
                                           id="inputTajwid"
                                           class="form-control form-control-lg fw-bold text-center rounded-3 @error('nilai_tajwid') is-invalid @enderror" 
                                           min="0" 
                                           max="100" 
                                           value="{{ old('nilai_tajwid', 85) }}" 
                                           oninput="document.getElementById('labelTajwid').innerText = this.value">
                                    @error('nilai_tajwid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="d-flex justify-content-center gap-1 mt-2">
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setScore('inputTajwid', 'labelTajwid', 95)">Mumtaz (95)</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setScore('inputTajwid', 'labelTajwid', 85)">Jayyid (85)</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setScore('inputTajwid', 'labelTajwid', 75)">Maqbul (75)</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nilai Kelancaran -->
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-bold text-secondary small mb-0">
                                            <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Kelancaran / Fasahah
                                        </label>
                                        <span class="badge bg-white text-dark border font-monospace" id="labelFluent">{{ old('nilai_fluent', 90) }}</span>
                                    </div>
                                    <input type="number" 
                                           name="nilai_fluent" 
                                           id="inputFluent"
                                           class="form-control form-control-lg fw-bold text-center rounded-3 @error('nilai_fluent') is-invalid @enderror" 
                                           min="0" 
                                           max="100" 
                                           value="{{ old('nilai_fluent', 90) }}" 
                                           oninput="document.getElementById('labelFluent').innerText = this.value">
                                    @error('nilai_fluent') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="d-flex justify-content-center gap-1 mt-2">
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setScore('inputFluent', 'labelFluent', 95)">Lancar (95)</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setScore('inputFluent', 'labelFluent', 85)">Cukup (85)</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill py-0 px-2" style="font-size: 0.7rem;" onclick="setScore('inputFluent', 'labelFluent', 75)">Bata-bata (75)</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nilai Adab & Sikap -->
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <label class="form-label fw-bold text-secondary small mb-1">
                                        <i class="bi bi-heart-fill text-danger me-1"></i>Adab &amp; Kesopanan
                                    </label>
                                    <select name="nilai_adab" class="form-select form-select-lg fw-semibold rounded-3 @error('nilai_adab') is-invalid @enderror">
                                        <option value="100" {{ (string)old('nilai_adab') === '100' ? 'selected' : '' }}>Mumtaz (100) - Istimewa</option>
                                        <option value="90" {{ (string)old('nilai_adab', '90') === '90' ? 'selected' : '' }}>Sangat Baik (90)</option>
                                        <option value="80" {{ (string)old('nilai_adab') === '80' ? 'selected' : '' }}>Baik &amp; Tertib (80)</option>
                                        <option value="70" {{ (string)old('nilai_adab') === '70' ? 'selected' : '' }}>Cukup (70)</option>
                                        <option value="60" {{ (string)old('nilai_adab') === '60' ? 'selected' : '' }}>Perlu Bimbingan (60)</option>
                                    </select>
                                    @error('nilai_adab') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted d-block text-center mt-2" style="font-size: 0.72rem;">
                                        Mencakup ketepatan waktu, kerapihan, dan kesantunan bicara.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Ujian Mutqin Checkbox -->
                        <div class="mt-4">
                            <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: rgba(234, 179, 8, 0.08); border-color: rgba(234, 179, 8, 0.3) !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="form-check form-switch fs-4 mb-0">
                                        <input class="form-check-input ms-0" type="checkbox" name="is_mutqin_test" value="1" id="is_mutqin_test" {{ old('is_mutqin_test') ? 'checked' : '' }}>
                                    </div>
                                    <label class="form-check-label fw-bold text-dark small mb-0 cursor-pointer" for="is_mutqin_test">
                                        <i class="bi bi-patch-check-fill text-warning me-1"></i> Tandai sebagai Ujian Kelulusan Mutqin Juz
                                        <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">
                                            Jika dicentang dan nilai tajwid &gt;= 80, santri otomatis dinyatakan Lulus Mutqin pada Juz yang dipilih.
                                        </span>
                                    </label>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 font-monospace" style="font-size: 0.72rem;">Sertifikasi Mutqin</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: Evaluasi Mentor & Tugas Rumah -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light-subtle pt-4 px-4 pb-3">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle bg-info-subtle text-info p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-chat-left-quote-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">4. Catatan Evaluasi &amp; Penugasan Murajaah</h6>
                                <small class="text-muted">Pesan ini akan langsung tampil di portal orang tua santri dan rekam rapor berkala</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small">
                                Catatan Evaluasi Mentor untuk Orang Tua
                            </label>
                            <textarea name="catatan_evaluasi" 
                                      class="form-control rounded-3 @error('catatan_evaluasi') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Contoh: Alhamdulillah ananda membaca dengan tartil. Pengucapan makhraj huruf 'Ain dan Ghain sudah semakin fasih. Perlu penguatan hukum mad thabi'i pada ayat-ayat panjang.">{{ old('catatan_evaluasi') }}</textarea>
                            @error('catatan_evaluasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="form-label fw-bold text-secondary small">
                                Target / Tugas Murajaah Mandiri di Rumah
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-house-door-fill"></i></span>
                                <input type="text" 
                                       name="homework" 
                                       class="form-control border-start-0 ps-0 @error('homework') is-invalid @enderror" 
                                       placeholder="Contoh: Murajaah hafalan Surah An-Naba ayat 1-20 sebanyak 3 kali bersama Ayah/Bunda sebelum tidur." 
                                       value="{{ old('homework') }}">
                            </div>
                            @error('homework') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Bottom Sticky Submit Bar -->
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-5">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('mentor.dashboard') }}" class="btn btn-light border rounded-pill px-4 fw-semibold text-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Batal &amp; Kembali
                        </a>
                        <button type="submit" class="btn btn-success rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-save2-fill"></i> Simpan Catatan Progres
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function selectKategoriUI(kategori) {
        const cards = {
            'Tahfidz': { id: 'katCardTahfidz', bg: '#ecfdf5', border: '#059669' },
            'Tahsin': { id: 'katCardTahsin', bg: '#eff6ff', border: '#2563eb' },
            'Iqra': { id: 'katCardIqra', bg: '#fffbeb', border: '#d97706' },
            'Adab': { id: 'katCardAdab', bg: '#fdf2f8', border: '#db2777' }
        };

        for (const [key, conf] of Object.entries(cards)) {
            const el = document.getElementById(conf.id);
            if (el) {
                if (key === kategori) {
                    el.style.backgroundColor = conf.bg;
                    el.style.borderColor = conf.border;
                } else {
                    el.style.backgroundColor = '#fafaf9';
                    el.style.borderColor = '#e5e7eb';
                }
            }
        }
    }

    function setJuz(num) {
        document.getElementById('inputJuz').value = num;
    }

    function setScore(inputId, labelId, value) {
        document.getElementById(inputId).value = value;
        document.getElementById(labelId).innerText = value;
    }

    function filterSessionsByStudent(studentId) {
        const sessionSelect = document.getElementById('selectSession');
        if (!sessionSelect) return;

        const options = sessionSelect.querySelectorAll('option');
        options.forEach(opt => {
            if (opt.value === '') return;
            const optStudentId = opt.getAttribute('data-student-id');
            if (!studentId || optStudentId === studentId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const currentStudent = document.getElementById('selectStudent')?.value;
        if (currentStudent) {
            filterSessionsByStudent(currentStudent);
        }
    });
</script>
@endsection
