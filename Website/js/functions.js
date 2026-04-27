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


// ------------ Dashboard Functions
let currentIndex = 0;
function cycleRecommendation() {
    const recommendationsSpan = document.getElementById('recommendation-text');

    if (recommendations !== null && recommendations.length > 0) {

        recommendationsSpan.innerHTML = recommendations[currentIndex];
        currentIndex = (currentIndex + 1) % recommendations.length;

    } else {
        recommendationsSpan.innerHTML = "No recommendations available for this week.";
    }
}
window.onload = cycleRecommendation;

function randomRecommendations() {
    const recommendationsSpan = document.getElementById('recommendation-text');
    recommendationsSpan.innerHTML = "";

    if (allRecommendations !== null && allRecommendations.length > 0) {

        let tempRecsArray = [...allRecommendations];

        for (let i = 0; i < 5; i++) {

            // pick out a random recommendation and remove it from the temp array to avoid duplicates
            randIndex = Math.floor(Math.random() * tempRecsArray.length);
            let pickedRec = tempRecsArray.splice(randIndex, 1)[0];
            recommendationsSpan.innerHTML += "<li>" + pickedRec + "</li>";

        }
    } else {
        recommendationsSpan.innerHTML = "Failed to retrieve random recommendations.";
    }
}


// ------------ Other Functions
function closeTC() {
    const overlay = document.getElementById('tc-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }

    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.pushState({ path: newUrl }, '', newUrl);
}