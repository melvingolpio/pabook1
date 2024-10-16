document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reservationModal');
    const closeButton = document.querySelector('.close-button');
    
    const cardBdy = document.querySelector('.card-bdy');
    cardBdy.addEventListener('click', function(){
        modal.style.display = 'block';
    });

    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});