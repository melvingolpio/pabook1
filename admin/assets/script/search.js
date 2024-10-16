document.getElementById('searchBox').addEventListener('input', function () {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#activitiesTable tbody tr');
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        let matchFound = false;
        columns.forEach(column => {
            if (column.textContent.toLowerCase().includes(searchValue)) {
                matchFound = true;
            }
        });
        if (matchFound) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

document.getElementById('searchBox2').addEventListener('input', function () {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#reservationsTable tbody tr');
    rows.forEach(row => {
        const columns = row.querySelectorAll('td');
        let matchFound = false;
        columns.forEach(column => {
            if (column.textContent.toLowerCase().includes(searchValue)) {
                matchFound = true;
            }
        });
        if (matchFound) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

function openModal() {
    document.getElementById("fullImageModal").style.display = "block";
}

function closeModal() {
    document.getElementById("fullImageModal").style.display = "none";
}