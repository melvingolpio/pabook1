function confirmSend(event) {
    event.preventDefault();

    if (confirm("Confirm renew?")) {
        setTimeout(function() {

            document.getElementById('centerMessage').classList.add('show');
            document.getElementById('modalOverlay').classList.remove('show');

            setTimeout(function() {
                document.getElementById('centerMessage').classList.remove('show');
                document.getElementById('modalOverlay').classList.remove('show');

                event.target.submit();
            }, 3000);
        }, 1000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    const clearSearch = document.getElementById('clearSearch');

    clearSearchBox.addEventListener('keyup', function() {
        let filer = this.value.toLowercase();
        let rows = document.querySelectorAll('#userTable tr');

        rows.forEach(row => {
            let id = parseInt(row.cells[0].textContent);
            let username = row.cells[1].textContent.tolowerCase();

            if (id.toString().includes(filter) || username.includes(filer)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        clearSearch.style.display = filter ? 'inline-block' : 'none';
    });
    clearSearch.addEventListener('click', function() {
        searchBox.value = '';
        rows.forEach(row => {
            row.style.display = '';
        });
        clearSearch.style.display = 'none';
    });
});