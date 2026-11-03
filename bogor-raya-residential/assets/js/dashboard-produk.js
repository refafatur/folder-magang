function toggleSidebar() {
        var sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('active');
    }

// Add property form toggle
function toggleAddForm() {
    const modal = document.getElementById('addPropertyModal');
    if (modal) {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
}

// Image preview function
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.classList.remove('d-none');
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

    // Search and Filter Functionality
    document.getElementById('searchInput').addEventListener('keyup', filterProperties);
    document.getElementById('filterPrice').addEventListener('change', filterProperties);
    document.getElementById('filterView').addEventListener('change', filterProperties);

    function filterProperties() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const priceFilter = document.getElementById('filterPrice').value;
        const viewFilter = document.getElementById('filterView').value;
        const cards = document.querySelectorAll('.product-card');

        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const price = parseInt(card.querySelector('.badge.bg-success').textContent.replace(/[^0-9]/g, ''));
            const view = card.querySelector('.badge.bg-info').textContent;
            
            let showCard = title.includes(search);
            
            if (priceFilter) {
                const [min, max] = priceFilter.split('-').map(Number);
                if (max) {
                    showCard = showCard && price >= min && price <= max;
                } else {
                    showCard = showCard && price >= min;
                }
            }
            
            if (viewFilter) {
                showCard = showCard && view === viewFilter;
            }
            
            card.closest('.col-md-4').style.display = showCard ? 'block' : 'none';
        });
    }

document.addEventListener('DOMContentLoaded', function() {
    // Configure modal event handlers
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const targetModalId = this.getAttribute('data-bs-target');
            const targetModal = document.querySelector(targetModalId);
            
            // Only modify behavior for add/edit unit modals
            if (targetModalId.includes('addUnitModal') || targetModalId.includes('editUnitModal')) {
                e.preventDefault();
                e.stopPropagation();

                // Find and hide parent modal (units list modal)
                const parentModal = document.querySelector('.modal.show');
                if (parentModal) {
                    parentModal.style.display = 'none';
                    // Remove parent modal backdrop
                    const parentBackdrop = document.querySelector('.modal-backdrop');
                    if (parentBackdrop) {
                        parentBackdrop.style.display = 'none';
                    }
                }

                // Show target modal
                const modal = new bootstrap.Modal(targetModal);
                modal.show();

                // Handle modal close
                targetModal.addEventListener('hidden.bs.modal', function() {
                    // Show parent modal and backdrop again
                    if (parentModal) {
                        parentModal.style.display = 'block';
                        if (parentBackdrop) {
                            parentBackdrop.style.display = 'block';
                        }
                    }
                }, { once: true });
            }
        });
    });
});