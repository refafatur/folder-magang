class ModalHandler {
    constructor() {
        this.initModals();
        this.initButtons();
    }

    initModals() {
        // Prevent multiple modal backdrops
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show.bs.modal', function (e) {
                const zIndex = 1040 + (10 * document.querySelectorAll('.modal:not(.modal-child)').length);
                this.style.zIndex = zIndex;
                setTimeout(() => {
                    const backdrop = document.querySelector('.modal-backdrop:last-child');
                    if (backdrop) {
                        backdrop.style.zIndex = zIndex - 1;
                    }
                });
            });

            modal.addEventListener('hidden.bs.modal', function () {
                if (document.querySelectorAll('.modal.show').length) {
                    document.body.classList.add('modal-open');
                }
            });
        });
    }

    initButtons() {
        // Add Property Button
        const addPropertyBtn = document.querySelector('.btn-add-property');
        if (addPropertyBtn) {
            addPropertyBtn.addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('addPropertyModal'));
                modal.show();
            });
        }

        // View Details Buttons
        document.querySelectorAll('.btn-view-details').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalId = btn.getAttribute('data-bs-target');
                const modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.show();
            });
        });

        // Units Modal Buttons
        document.querySelectorAll('.btn-units').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = btn.getAttribute('data-bs-target');
                const modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.show();
            });
        });

        // Edit Buttons
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalId = btn.getAttribute('data-bs-target');
                const modal = new bootstrap.Modal(document.querySelector(modalId));
                modal.show();
            });
        });

        // Delete Unit Buttons
        document.querySelectorAll('.btn-delete-unit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const unitId = btn.getAttribute('data-unit-id');
                this.confirmDelete(unitId);
            });
        });
    }

    confirmDelete(unitId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `?delete_unit=${unitId}`;
            }
        });
    }
}
