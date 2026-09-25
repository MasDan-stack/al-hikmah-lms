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

        let mergedOptions = Object.assign({}, window.defaultDataTableOptions, options);

        // 1. HANDLER data-no-paging / data-no-info
        if (el.hasAttribute('data-no-paging') || el.dataset.noPaging === 'true') {
            mergedOptions.paging = false;
            mergedOptions.info = false;
        }

        // 2. HANDLER data-no-search
        if (el.hasAttribute('data-no-search') || el.dataset.noSearch === 'true') {
            mergedOptions.searching = false;
        }

        // 3. HANDLER data-no-info
        if (el.hasAttribute('data-no-info') || el.dataset.noInfo === 'true') {
            mergedOptions.info = false;
        }

        // 4. Safe check for DataTables Buttons plugin
        const hasButtonsPlugin = (typeof window.$ !== 'undefined' && typeof window.$.fn !== 'undefined' && typeof window.$.fn.dataTable !== 'undefined' && typeof window.$.fn.dataTable.Buttons !== 'undefined') || (typeof DataTable !== 'undefined' && typeof DataTable.Buttons !== 'undefined');

        if ((el.hasAttribute('data-export') || el.dataset.export === 'true') && hasButtonsPlugin) {
            mergedOptions.layout = {
                topStart: 'pageLength',
                topEnd: {
                    search: true,
                    buttons: [
                        { extend: 'excelHtml5', className: 'btn btn-sm btn-outline-success rounded-3 me-1', text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel' },
                        { extend: 'pdfHtml5', className: 'btn btn-sm btn-outline-danger rounded-3 me-1', text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF' },
                        { extend: 'print', className: 'btn btn-sm btn-outline-secondary rounded-3', text: '<i class="bi bi-printer me-1"></i> Cetak' }
                    ]
                }
            };
        }

        try {
            const instance = new DataTable(el, mergedOptions);
            el.__dataTable = instance;

            // Jika data-export="true" dan tanpa plugin Buttons berat, gunakan export toolbar vanilla kami
            if ((el.hasAttribute('data-export') || el.dataset.export === 'true') && !hasButtonsPlugin) {
                attachVanillaExportToolbar(instance, el);
            }

            // Tambahkan tooltip pada header kolom yang dapat di-sort
            el.querySelectorAll('thead th:not(.no-sort)').forEach(function (th) {
                if (!th.hasAttribute('title')) {
                    th.setAttribute('title', 'Klik untuk mengurutkan');
                }
            });

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
     * Helper Toolbar Ekspor CSV & Print tanpa dependensi jQuery / Buttons
     */
    function attachVanillaExportToolbar(instance, tableEl) {
        if (!tableEl || tableEl.dataset.exportAttached === 'true') return;
        tableEl.dataset.exportAttached = 'true';

        const container = tableEl.closest('.dt-container') || tableEl.parentElement;
        if (!container) return;

        const toolbar = document.createElement('div');
        toolbar.className = 'dt-export-toolbar';
        toolbar.innerHTML = `
            <button type="button" class="dt-btn-export btn-export-csv" title="Ekspor ke format Excel / CSV">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
            </button>
            <button type="button" class="dt-btn-export btn-export-print" title="Cetak Data">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
        `;

        const targetCell = container.querySelector('.dt-layout-row .dt-layout-end') 
            || container.querySelector('.dt-layout-row .dt-layout-start');

        if (targetCell) {
            targetCell.prepend(toolbar);
        } else {
            tableEl.insertAdjacentElement('beforebegin', toolbar);
        }

        const tableId = tableEl.getAttribute('id') || 'data-tabel';
        const pageTitle = document.title ? document.title.split('|')[0].trim() : 'Data AL-HIKMAH';

        toolbar.querySelector('.btn-export-csv')?.addEventListener('click', function () {
            window.exportDataTableCSV(instance, tableId);
        });

        toolbar.querySelector('.btn-export-print')?.addEventListener('click', function () {
            window.printDataTable(instance, pageTitle);
        });
    }

    /**
     * Ekspor data tabel ke CSV dengan UTF-8 BOM agar kompatibel dengan Microsoft Excel
     */
    window.exportDataTableCSV = function (instance, filename) {
        if (!instance) return;
        const tableEl = instance.table().node();
        const headers = [];
        const headerCols = [];

        tableEl.querySelectorAll('thead th').forEach((th, idx) => {
            const text = th.textContent.trim();
            if (!th.classList.contains('no-sort') && !text.toLowerCase().includes('aksi')) {
                headers.push('"' + text.replace(/"/g, '""') + '"');
                headerCols.push(idx);
            }
        });

        const rows = [headers.join(',')];

        instance.rows({ search: 'applied' }).every(function () {
            const rowNode = this.node();
            if (!rowNode) return;
            const rowData = [];
            headerCols.forEach(colIdx => {
                const cell = rowNode.cells[colIdx];
                if (cell) {
                    let text = cell.innerText || cell.textContent || '';
                    text = text.trim().replace(/\r?\n|\r/g, ' ').replace(/\s+/g, ' ');
                    rowData.push('"' + text.replace(/"/g, '""') + '"');
                } else {
                    rowData.push('""');
                }
            });
            rows.push(rowData.join(','));
        });

        const csvContent = '\uFEFF' + rows.join('\r\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', (filename || 'export-data') + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    };

    /**
     * Cetak data tabel dengan layout cetak yang bersih
     */
    window.printDataTable = function (instance, title) {
        if (!instance) return;
        const tableEl = instance.table().node();
        const printWin = window.open('', '_blank', 'width=960,height=720');
        if (!printWin) {
            window.print();
            return;
        }

        const clonedTable = tableEl.cloneNode(true);
        clonedTable.querySelectorAll('.no-sort').forEach(el => el.remove());
        clonedTable.querySelectorAll('tr').forEach(tr => {
            const lastTd = tr.querySelector('td:last-child');
            if (lastTd && lastTd.classList.contains('no-sort')) {
                lastTd.remove();
            }
        });

        printWin.document.write(`
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="utf-8">
                <title>${title || 'Cetak Data AL-HIKMAH'}</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 24px; color: #1e293b; }
                    h2 { margin: 0 0 4px 0; color: #0d7a3e; font-size: 20px; }
                    p { font-size: 13px; color: #64748b; margin: 0 0 18px 0; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
                    th { background-color: #f8fafc; font-weight: 600; color: #334155; }
                    tr:nth-child(even) { background-color: #f8fafc; }
                    .badge { font-weight: 600; font-size: 11px; }
                </style>
            </head>
            <body>
                <h2>AL-HIKMAH LMS</h2>
                <p>${title || 'Laporan Data Sistem'} &bull; Dicetak pada: ${new Date().toLocaleString('id-ID')}</p>
                ${clonedTable.outerHTML}
                <script>
                    window.onload = function() { window.print(); window.close(); };
                <\/script>
            </body>
            </html>
        `);
        printWin.document.close();
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
