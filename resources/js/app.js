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
