document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('navbar-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            const isOpen = navMenu.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    document.querySelectorAll('[data-dismiss-alert]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const alertEl = btn.closest('.alert');
            if (alertEl) {
                alertEl.remove();
            }
        });
    });

    const modal = document.getElementById('confirm-modal');
    const modalTitle = document.getElementById('confirm-modal-title');
    const modalText = document.getElementById('confirm-modal-text');
    const modalConfirmBtn = document.getElementById('confirm-modal-submit');
    const modalCancelBtn = document.getElementById('confirm-modal-cancel');
    let pendingForm = null;

    const closeModal = () => {
        if (modal) {
            modal.classList.remove('open');
        }
        pendingForm = null;
    };

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!modal || form.dataset.confirmed === 'true') {
                return;
            }
            event.preventDefault();
            pendingForm = form;
            modalTitle.textContent = form.dataset.confirmTitle || 'Konfirmasi Aksi';
            modalText.textContent = form.dataset.confirm || 'Apakah Anda yakin ingin melanjutkan?';
            modalConfirmBtn.textContent = form.dataset.confirmLabel || 'Ya, Lanjutkan';
            modalConfirmBtn.className = 'btn ' + (form.dataset.confirmVariant || 'btn-primary');
            modal.classList.add('open');
        });
    });

    if (modalConfirmBtn) {
        modalConfirmBtn.addEventListener('click', () => {
            if (pendingForm) {
                pendingForm.dataset.confirmed = 'true';
                pendingForm.submit();
            }
        });
    }

    if (modalCancelBtn) {
        modalCancelBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    }
});
