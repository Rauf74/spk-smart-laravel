/**
 * Auth Login Module
 * - Password toggle
 * - Quick Login Demo modal (custom HTML, 3-step: role → loading → credential)
 */
export function initAuthLogin() {

    // ─── Password Toggle ───────────────────────────────────────────
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

    // ─── Quick Login Demo Modal ─────────────────────────────────────
    const overlay    = document.getElementById('qlOverlay');
    const stepRole   = document.getElementById('qlStepRole');
    const stepLoad   = document.getElementById('qlStepLoading');
    const stepOk     = document.getElementById('qlStepSuccess');

    if (!overlay) return;  // modal HTML tidak ada, skip

    const openBtn    = document.getElementById('btnQuickLoginDemo');
    const cancelBtn  = document.getElementById('qlBtnCancel');
    const guruBtn    = document.getElementById('qlBtnGuru');
    const siswaBtn   = document.getElementById('qlBtnSiswa');
    const enterBtn   = document.getElementById('qlBtnEnter');
    const copyAllBtn = document.getElementById('qlCopyAll');

    let redirectUrl  = '/';
    let lastUsername = '';
    let lastPassword = '';

    // Helpers
    function showStep(step) {
        [stepRole, stepLoad, stepOk].forEach(s => s?.classList.remove('active'));
        step?.classList.add('active');
    }

    function openModal() {
        showStep(stepRole);
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.textContent;
            btn.textContent = '✓ Disalin';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('copied');
            }, 1800);
        }).catch(() => {
            // Fallback untuk browser yang tidak support clipboard API
            const el = document.createElement('textarea');
            el.value = text;
            el.style.position = 'fixed';
            el.style.opacity = '0';
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            const orig = btn.textContent;
            btn.textContent = '✓ Disalin';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('copied');
            }, 1800);
        });
    }

    function generateAccount(role) {
        showStep(stepLoad);

        fetch('/login/quick-generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ role })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'success') throw new Error(data.message || 'Gagal membuat akun demo.');

            // Simpan untuk copy-all
            lastUsername = data.username;
            lastPassword = data.password;
            redirectUrl  = data.redirect || '/';

            // Isi credential display
            const isGuru  = data.role === 'Guru BK';
            const badge   = document.getElementById('qlValRoleBadge');
            document.getElementById('qlValNama').textContent     = data.nama_user;
            document.getElementById('qlValUsername').textContent = data.username;
            document.getElementById('qlValPassword').textContent = data.password;
            badge.textContent  = data.role;
            badge.className    = 'ql-cred-badge ' + (isGuru ? 'guru' : 'siswa');

            // Confetti ringan
            if (typeof confetti !== 'undefined') {
                confetti({ particleCount: 60, spread: 55, origin: { y: 0.55 }, zIndex: 10000 });
            }

            showStep(stepOk);
        })
        .catch(err => {
            closeModal();
            // Fallback ke SweetAlert jika tersedia
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Terjadi kesalahan. Silakan coba lagi.',
                    customClass: { popup: 'rounded-4' }
                });
            } else {
                alert('Gagal membuat akun demo: ' + (err.message || 'Coba lagi.'));
            }
        });
    }

    // ─── Event Listeners ──────────────────────────────────────────
    openBtn?.addEventListener('click', openModal);
    cancelBtn?.addEventListener('click', closeModal);

    // Tutup saat klik overlay (bukan modal)
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    // Tutup dengan Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) closeModal();
    });

    guruBtn?.addEventListener('click',  () => generateAccount('guru'));
    siswaBtn?.addEventListener('click', () => generateAccount('siswa'));

    enterBtn?.addEventListener('click', () => {
        window.location.href = redirectUrl;
    });

    // Copy individual
    document.querySelectorAll('.ql-copy-btn[data-target]').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const el = document.getElementById(targetId);
            if (el) copyText(el.textContent.trim(), btn);
        });
    });

    // Copy all
    copyAllBtn?.addEventListener('click', () => {
        const text = `Username: ${lastUsername}\nPassword: ${lastPassword}`;
        copyText(text, copyAllBtn);
    });
}
