export function initConfirmDelete() {
    document.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.js-confirm-delete');
        if (deleteBtn && typeof Swal !== 'undefined') {
            e.preventDefault();
            const form = deleteBtn.closest('form');
            const itemName = deleteBtn.getAttribute('data-item') || 'data ini';

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: `Anda akan menghapus ${itemName}. Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FA896B',
                cancelButtonColor: '#5D87FF',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        }
    });
}
