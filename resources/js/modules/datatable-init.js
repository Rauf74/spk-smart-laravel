export function initDataTables() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
        const tables = document.querySelectorAll('.js-datatable');
        tables.forEach(table => {
            if (!jQuery.fn.DataTable.isDataTable(table)) {
                jQuery(table).DataTable({
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 data",
                        zeroRecords: "Data tidak ditemukan",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            }
        });
    }
}
