function fetchReceiptData(plateNumber) {
    fetch(`get_receipt.php?plate_number=${'plateNumber'}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                alert("Failed to fetch receipt data: " + data.error);
                return;
            }
            populateModal(data);
            showModal();
        })
        .catch(error => {
            console.error("Error fetching receipt data:", error);
            alert("Failed to fetch receipt data. Please try again.");
        });
}

document.querySelectorAll('.box').forEach(box => {
    box.addEventListener('click', () => {
        const plateNumber = box.getAttribute('data-plate-number');
        fetchReceiptData(plateNumber);
    });
});


function populateModal(data) {
    document.getElementById('receiptId').textContent = data.id;
    document.getElementById('plateNumber').textContent = data.plate_number;
    document.getElementById('username').textContent = data.username;
    document.getElementById('fullname').textContent = data.fullname;
    document.getElementById('age').textContent = data.age;
    document.getElementById('gender').textContent = data.gender;
    document.getElementById('contactNumber').textContent = data.contact_number;
    document.getElementById('type').textContent = data.type;
    document.getElementById('email').textContent = data.email;
    document.getElementById('createdAt').textContent = data.created_at;
    document.getElementById('expirationDate').textContent = data.expiration_date;
    document.getElementById('qrCode').src = `data:image/png;base64,${data.qr_code}`;
}


function showModal() {
    const modal = document.getElementById('receiptModal');
    modal.style.display = 'block';

    const closeBtn = document.querySelector('.close');
    closeBtn.onclick = function () {
        modal.style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}
