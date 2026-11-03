// document.addEventListener('DOMContentLoaded', function() {
//     const users = <?php echo json_encode($users); ?>;
    
//     window.editUser = function(userId) {
//         const user = users.find(u => u.id === userId);
//         if (!user) return;

//         const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
//         const modalBody = document.querySelector('#editUserModal .modal-body');
        
//         modalBody.innerHTML = `
//             <form method="POST" action="">
//                 <input type="hidden" name="user_id" value="${user.id}">
//                 <div class="mb-3">
//                     <label class="form-label">Username</label>
//                     <input type="text" class="form-control" value="${user.username}" disabled>
//                 </div>
//                 <div class="mb-3">
//                     <label class="form-label">New Password (leave blank to keep current)</label>
//                     <input type="password" class="form-control" name="password">
//                 </div>
//                 <div class="mb-3">
//                     <label class="form-label">Full Name</label>
//                     <input type="text" class="form-control" name="name" value="${user.name}" required>
//                 </div>
//                 <div class="mb-3">
//                     <label class="form-label">Email</label>
//                     <input type="email" class="form-control" name="email" value="${user.email}" required>
//                 </div>
//                 <div class="mb-3">
//                     <label class="form-label">Phone</label>
//                     <input type="text" class="form-control" name="phone" value="${user.phone}" required>
//                 </div>
//                 <div class="row">
//                     <div class="col-md-6">
//                         <div class="mb-3">
//                             <label class="form-label">Gender</label>
//                             <select class="form-select" name="gender" required>
//                                 <option value="male" ${user.gender === 'male' ? 'selected' : ''}>Male</option>
//                                 <option value="female" ${user.gender === 'female' ? 'selected' : ''}>Female</option>
//                             </select>
//                         </div>
//                     </div>
//                     <div class="col-md-6">
//                         <div class="mb-3">
//                             <label class="form-label">Role</label>
//                             <select class="form-select" name="role" required>
//                                 <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
//                                 <option value="sales" ${user.role === 'sales' ? 'selected' : ''}>Sales</option>
//                                 <option value="user" ${user.role === 'user' ? 'selected' : ''}>User</option>
//                             </select>
//                         </div>
//                     </div>
//                 </div>
//                 <div class="row">
//                     <div class="col-md-6">
//                         <div class="mb-3">
//                             <label class="form-label">Status</label>
//                             <select class="form-select" name="status" required>
//                                 <option value="active" ${user.status === 'active' ? 'selected' : ''}>Active</option>
//                                 <option value="inactive" ${user.status === 'inactive' ? 'selected' : ''}>Inactive</option>
//                             </select>
//                         </div>
//                     </div>
//                     <div class="col-md-6">
//                         <div class="mb-3">
//                             <label class="form-label">Extend Expiry (months)</label>
//                             <input type="number" class="form-control" name="expiry_months" value="12" required>
//                         </div>
//                     </div>
//                 </div>
//                 <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
//             </form>
//         `;
        
//         modal.show();
//     };

//     window.deleteUser = function(userId) {
//         const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
//         document.getElementById('deleteUserId').value = userId;
//         modal.show();
//     };

//     window.viewUser = function(userId) {
//         const user = users.find(u => u.id === userId);
//         if (!user) return;
        
//         // Implement view functionality
//         alert('View user details: ' + user.name);
//     };
// });

// function togglePassword() {
//     const passwordInput = document.getElementById('password');
//     const icon = document.querySelector('.fa-eye');
    
//     if (passwordInput.type === 'password') {
//         passwordInput.type = 'text';
//         icon.classList.replace('fa-eye', 'fa-eye-slash');
//     } else {
//         passwordInput.type = 'password';
//         icon.classList.replace('fa-eye-slash', 'fa-eye');
//     }
// }

// document.getElementById('createUserForm').addEventListener('submit', function(e) {
//     const submitBtn = this.querySelector('button[type="submit"]');
//     submitBtn.disabled = true;
//     submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
// });

// // Auto-hide alerts after 5 seconds
// document.querySelectorAll('.alert').forEach(alert => {
//     setTimeout(() => {
//         alert.classList.remove('show');
//         setTimeout(() => alert.remove(), 150);
//     }, 5000);
// });