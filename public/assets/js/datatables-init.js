/**
 * AL-HIKMAH LMS - DataTables Global Helper & Initializer
 * Integrasi resmi DataTables + Bootstrap 5 + Responsive
 */

(function () {
    'use strict';

    // Konfigurasi Default DataTables Berbahasa Indonesia & Responsif
    window.defaultDataTableOptions = {
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [
            [5, 10, 25, 50, -1],
            [5, 10, 25, 50, "Semua"]
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari data...",
            lengthMenu: "Tampilkan _MENU_ baris",
            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok ditemukan",
            emptyTable: "Tidak ada data yang tersedia pada tabel ini",
            loadingRecords: "Memuat data...",
            processing: "Sedang memproses...",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            }
        },
        order: [], // Pertahankan urutan awal dari HTML server
        columnDefs: [
            {
                targets: ['.no-sort', 'no-sort'],
                orderable: false,
                searchable: false
            }
        ]
    };

    /**
     * Helper untuk inisialisasi DataTable pada satu elemen atau selector
     * @param {string|HTMLElement} target Selector string atau elemen DOM
     * @param {Object} options Opsi kustom untuk override default
     * @returns {DataTable|null}
     */
    window.initDataTable = function (target, options = {}) {
        if (typeof DataTable === 'undefined') {
            console.warn('DataTables library belum dimuat.');
            return null;
        }

        const el = typeof target === 'string' ? document.querySelector(target) : target;
        if (!el) return null;

        // Pastikan tabel memiliki thead dan tbody
        const thead = el.querySelector('thead');
        const tbody = el.querySelector('tbody');
        if (!thead || !tbody) {
            return null;
        }

        // Cek jika tabel kosong dengan baris colspan bawaan Blade (@empty)
        const emptyColspan = tbody.querySelector('tr td[colspan]');
        const totalRows = tbody.querySelectorAll('tr').length;
        if (emptyColspan && totalRows === 1) {
            // Kosongkan tbody agar DataTables tidak mengalami error mismatch jumlah kolom
            tbody.innerHTML = '';
        }

        // Jika sudah diinisialisasi sebelumnya, hancurkan dulu sebelum init ulang
        if (DataTable.isDataTable(el)) {
            try {
                const oldInstance = new DataTable(el);
                oldInstance.destroy();
            } catch (e) {
                // Ignore destroy error
            }
        }

        const mergedOptions = Object.assign({}, window.defaultDataTableOptions, options);
        try {
            const instance = new DataTable(el, mergedOptions);

            // Sesuaikan kolom saat window resize
            window.addEventListener('resize', () => {
                try {
                    if (instance && instance.columns) {
                        instance.columns.adjust().responsive.recalc();
                    }
                } catch (e) {}
            });

            return instance;
        } catch (err) {
            console.error('Error saat inisialisasi DataTable:', err);
            return null;
        }
    };

    /**
     * Inisialisasi otomatis semua tabel dengan class .datatable atau [data-datatable]
     */
    function autoInitDataTables() {
        if (typeof DataTable === 'undefined') return;

        const tables = document.querySelectorAll('table.datatable, table[data-datatable]');
        tables.forEach((table) => {
            if (!DataTable.isDataTable(table)) {
                // Baca opsi custom dari data-attribute jika ada
                let customOpts = {};
                if (table.dataset.pageLength) {
                    customOpts.pageLength = parseInt(table.dataset.pageLength, 10);
                }
                if (table.dataset.noSearch === 'true') {
                    customOpts.searching = false;
                }
                if (table.dataset.noPaging === 'true') {
                    customOpts.paging = false;
                }
                if (table.dataset.noInfo === 'true') {
                    customOpts.info = false;
                }
                window.initDataTable(table, customOpts);
            }
        });
    }

    // Eksekusi auto init saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInitDataTables);
    } else {
        autoInitDataTables();
    }

    // Auto recalculate saat Bootstrap Tab / Pill diaktifkan
    document.addEventListener('shown.bs.tab', function (event) {
        const targetSelector = event.target.getAttribute('data-bs-target') || event.target.getAttribute('href');
        if (targetSelector && targetSelector.startsWith('#')) {
            const tabPane = document.querySelector(targetSelector);
            if (tabPane) {
                const tables = tabPane.querySelectorAll('table.dataTable, table.datatable');
                tables.forEach((table) => {
                    if (typeof DataTable !== 'undefined') {
                        if (DataTable.isDataTable(table)) {
                            const dt = new DataTable(table);
                            dt.columns.adjust().responsive.recalc();
                        } else if (table.classList.contains('datatable') || table.hasAttribute('data-datatable')) {
                            window.initDataTable(table);
                        }
                    }
                });
            }
        }
    });

    // Auto recalculate saat Bootstrap Modal ditampilkan
    document.addEventListener('shown.bs.modal', function (event) {
        const modal = event.target;
        if (modal) {
            const tables = modal.querySelectorAll('table.dataTable, table.datatable');
            tables.forEach((table) => {
                if (typeof DataTable !== 'undefined') {
                    if (DataTable.isDataTable(table)) {
                        const dt = new DataTable(table);
                        dt.columns.adjust().responsive.recalc();
                    } else if (table.classList.contains('datatable') || table.hasAttribute('data-datatable')) {
                        window.initDataTable(table);
                    }
                }
            });
        }
    });

    // Dukungan navigasi Livewire jika halaman berpindah
    document.addEventListener('livewire:navigated', autoInitDataTables);
})();
