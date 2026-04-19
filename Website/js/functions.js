// ------------ General functions
function openSideBar() {
    document.getElementById("sidebar-container").style.width = "270px";
}

function closeSideBar() {
    document.getElementById("sidebar-container").style.width = "0";
}


// ------------ Activity Log Functions
function openEditModal(id, date, value, notes) {
    document.getElementById('edit_log_id').value = id;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_value').value = value;
    document.getElementById('edit_notes').value = notes;
    document.getElementById('editModal').style.display = 'flex';
}
function openAddModal() {
    document.getElementById('addModal').style.display = 'flex';
}
function confirmDelete(id) {
    document.getElementById('delete_log_id').value = id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
window.onclick = function (e) {
    ['editModal', 'addModal', 'deleteModal'].forEach(function (id) {
        var m = document.getElementById(id);
        if (e.target === m) m.style.display = 'none';
    });
}