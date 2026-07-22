export function initAuthLogin() {
    // Password toggle
    const toggleBtn = document.getElementById('togglePassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password && icon) {
                if (password.type === 'password') {
                    password.type = 'text';
                    icon.classList.replace('ti-eye', 'ti-eye-off');
                } else {
                    password.type = 'password';
                    icon.classList.replace('ti-eye-off', 'ti-eye');
                }
            }
        });
    }

    // Quick Login Demo SweetAlert2 Centered Popup
    const quickLoginBtn = document.getElementById('btnQuickLoginDemo');
    if (quickLoginBtn && typeof Swal !== 'undefined') {
        quickLoginBtn.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Pilih Peran Demo',
                text: 'Silakan pilih peran akun demo yang ingin dibuat:',
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<i class="ti ti-user-star me-1"></i> Guru BK',
                confirmButtonColor: '#5D87FF',
                denyButtonText: '<i class="ti ti-school me-1"></i> Siswa',
                denyButtonColor: '#13DEB9',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                let chosenRole = null;
                if (result.isConfirmed) {
                    chosenRole = 'guru';
                } else if (result.isDenied) {
                    chosenRole = 'siswa';
                }

                if (chosenRole) {
                    Swal.fire({
                        title: '<div class="spinner-border text-primary" role="status"></div>',
                        html: '<p class="mb-0 text-muted">Membuat akun demo acak...</p>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        customClass: { popup: 'rounded-4' }
                    });

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    fetch('/login/quick-generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ role: chosenRole })
                    })
                    .then(res => res.json())
                    .then(response => {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '🎉 Akun Demo Berhasil Dibuat!',
                                html: `
                                    <p class="text-muted small mb-3">Sistem telah otomatis menghasilkan kredensial berikut:</p>
                                    <div class="card bg-light border p-3 mb-3 text-start">
                                        <div class="mb-2"><strong>Nama:</strong> ${response.nama_user}</div>
                                        <div class="mb-2"><strong>Role:</strong> <span class="badge ${response.role === 'Guru BK' ? 'bg-primary' : 'bg-success'}">${response.role}</span></div>
                                        <div class="mb-2"><strong>Username:</strong> <code class="fs-5 text-primary fw-bold">${response.username}</code></div>
                                        <div><strong>Password:</strong> <code class="fs-5 text-danger fw-bold">${response.password}</code></div>
                                    </div>
                                    <p class="text-muted small mb-0">Klik tombol di bawah ini untuk langsung menuju ke Dashboard.</p>
                                `,
                                confirmButtonText: '<i class="ti ti-login me-1"></i> Masuk ke Dashboard',
                                confirmButtonColor: '#5D87FF',
                                allowOutsideClick: false,
                                customClass: { popup: 'rounded-4' }
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat membuat akun demo. Silakan coba lagi.',
                            customClass: { popup: 'rounded-4' }
                        });
                    });
                }
            });
        });
    }
}
