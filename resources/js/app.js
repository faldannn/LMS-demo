import './bootstrap';

window.toggleSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (!sidebar || !overlay) {
        return;
    }

    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
};

window.addEventListener('DOMContentLoaded', () => {
    const closeModal = (modal) => {
        if (!modal) {
            return;
        }

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-bs-toggle="modal"]');
        if (!trigger) {
            return;
        }

        event.preventDefault();
        const modal = document.querySelector(trigger.dataset.bsTarget);
        if (!modal) {
            return;
        }

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    });

    document.addEventListener('click', (event) => {
        const dismiss = event.target.closest('[data-bs-dismiss="modal"]');
        if (dismiss) {
            event.preventDefault();
            closeModal(dismiss.closest('.modal'));
            return;
        }

        if (event.target.classList.contains('modal')) {
            closeModal(event.target);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal.show').forEach(closeModal);
        }
    });

    document.addEventListener('click', (event) => {
        const target = event.target.closest('[data-confirm]');
        if (!target) {
            return;
        }

        event.preventDefault();
        const formId = target.dataset.form;
        const form = formId ? document.getElementById(formId) : null;

        Swal.fire({
            title: 'Konfirmasi',
            text: target.dataset.confirm || 'Apakah Anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                if (form) {
                    form.submit();
                } else if (target.tagName === 'A' && target.href) {
                    window.location.href = target.href;
                }
            }
        });
    });

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach((alertEl) => {
            const alertInstance = window.bootstrap?.Alert.getOrCreateInstance(alertEl);
            alertInstance?.close();
        });
    }, 5000);
});
