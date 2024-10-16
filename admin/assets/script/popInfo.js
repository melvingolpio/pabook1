    const modal = document.getElementById('reservationModal');
    const modalContent = document.getElementById('modalContent');
    const closeButton = document.querySelector('.close-button');

    fetch(`get_user_details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                modalContent.innerHTML = `<p>Error: ${data.error}</p>`;
            } else {
                let vehiclesHtml = '';
                if (data.vehicles && data.vehicles.length > 0) {
                    data.vehicles.forEach(vehicle => {
                        vehiclesHtml += `
                            <div class="vehicle-card">
                                <p><strong>Plate Number: </strong>${vehicle.plate_number || 'N/A'}</p>
                                <p><strong>Vehicle Brand: </strong>${vehicle.vehicle_brand || 'N/A'}</p>
                                <p><strong>Vehicle Type: </strong>${vehicle.vehicle_type || 'N/A'}</p>
                                <p><strong>Color: </strong>${vehicle.color || 'N/A'}</p>
                                <p><strong>Amount: </strong>${vehicle.amount || 'N/A'}</p>
                                <p><strong>Paid: </strong>${vehicle.paid || 'N/A'}</p>
                            </div>
                        `;
                    });
                } else {
                    vehiclesHtml = '<p>No vehicles found.</p>';
                }

                modalContent.innerHTML = `
                    <p><strong>Username: </strong>${data.user.username || 'N/A'}</p>
                    <p><strong>Full Name: </strong>${data.user.full_name || 'N/A'}</p>
                    <p><strong>Gender: </strong>${data.user.gender || 'N/A'}</p>
                    <p><strong>Age: </strong>${data.user.age || 'N/A'}</p>
                    <p><strong>Contact Number: </strong>${data.user.contact_number || 'N/A'}</p>
                    <p><strong>Type: </strong>${data.user.type || 'N/A'}</p>
                    <p><strong>Email: </strong>${data.user.email || 'N/A'}</p>
                    <p><strong>Image: </strong><img src="../img/${data.user.image || 'default.jpg'}" alt="Profile Picture" style="width:100px;"></p>
                    <p><strong>Penalty: </strong>${data.user.penalty || 'N/A'}</p>
                    <h3>Vehicles:</h3>
                    ${vehiclesHtml}
                `;
            }
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching user details:', error);
            modalContent.innerHTML = `<p>Error fetching user details. Please try again later.</p>`;
            modal.style.display = 'block';
        });

    closeButton.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}
