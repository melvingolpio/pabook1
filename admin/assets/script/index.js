document.querySelector(".menuicn");
let nav = document.querySelector(".navcontainer");

menuicn.addEventListener("click", () => {
    nav.classList.toggle("navclose");
})

document.addEventListener("DOMContentLoaded", function() {
    const navOptions = document.querySelectorAll('.nav-option');

    navOptions.forEach(option => {
        option.addEventListener('click', function() {
            navOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {

    const modal = document.getElementById('updateModal');
    const classModal = document.querySelector('.btnUpdate');
    const closeBtn = document.querySelector('.close-button3');

    classModal.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});

//user_account.php//

function openModal(userId) {
    const modal = document.getElementById('reservationModal');
    const modalContent = document.getElementById('modalContent');
    const closeButton = document.querySelector('.close-button');

    fetch(`get_user_details.php?id=${userId}`)
    .then(response => response.json())
    .then(data => {
        
        if (data.error) {
            modalContent.innerHTML = `<p>ERROR: ${data.error}</p>`;
        } else {
            let vehiclesHtml = '';
            document.getElementById('userIdField').value = data.user.id;
            document.getElementById('licenseId').value = data.user.license_number || '';
            document.getElementById('birthDate').value = data.user.birth_date || '';    

            if (data.vehicles && data.vehicles.length > 0) {
                data.vehicles.forEach(vehicle => {
                    vehiclesHtml += `
                            
                            <p><strong>Plate Number: </strong>${vehicle.plate_number || 'N/A'}</p>
                            <p><strong>Vehicle Brand: </strong>${vehicle.vehicle_brand || 'N/A'}</p>
                            <p><strong>Vehicle Type: </strong>${vehicle.vehicle_type || 'N/A'}</p>
                            <p><strong>Color: </strong>${vehicle.color || 'N/A'}</p>
                            <p><strong>Amount: </strong>${vehicle.amount || 'N/A'}</p>
                            <p><strong>Paid: </strong>${vehicle.paid || 'N/A'}</p>
                            <br><hr>
                    
                    `;
                });
            } else {
                vehiclesHtml = '<p>No vehicles found.</p>';
            }

            modalContent.innerHTML = `
            <div class="vehicles-list">
            <p><strong></strong><img src="../img/${data.user.image || 'default.jpg'}" alt="Profile picture" class="imageJs"></p>
            <p><strong>Username: </strong>${data.user.username || 'N/A'}</p>
            <p><strong>Full Name: </strong>${data.user.full_name || 'N/A'}</p>
            <p><strong>Gender: </strong>${data.user.gender || 'N/A'}</p>
            <p><strong>Birth Date: </strong>${data.user.birth_date || 'N/A'}</p>
            <p><strong>Contanct Number: </strong>${data.user.contact_number || 'N/A'}</p>
            <p><strong>Type: </strong>${data.user.type || 'N/A'}</p>
            <p><strong>Email: </strong>${data.user.email || 'N/A'}</p>
            <p><strong>LTO Registration: </strong>${data.user.lto_registration || 'N/A'}</p>
            <p><strong>Licese Number: </strong>${data.user.license || 'N/A'}</p>
            <p><strong>Penalty: </strong>${data.user.penalty || 'N/A'}</p>
            <p><strong>Restricted: </strong>${data.user.restricted || 'N/A'}</p>
            
            <hr><br>
            <h3>Vehicles:</h3>
            
            ${vehiclesHtml}
        </div>
            `;
        }

        modal.style.display = 'block';
    })
    .catch(error => {
        console.error('Error fetching user details:', error);
        modalContent.innerHTML = `<p>Error fetching user details</p>`;
        modal.style.display = 'block';
    });

    closeButton.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
}

