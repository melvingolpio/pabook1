
function restrictUser (button) {
    var userId = button.getAttribute('data-id');

    fetch('restrict_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'user_id' + encodeURIComponent(userId)
    })
    .then(response => response.text())
    .then(data => {
        button.classList.add('btn-restrict');
        alert(data);
    })
    .catch(error=>console.error('Error:', error));
}


function disableUser (button) {
    var userId = button.getAttribute('data-id');

    fetch('disable_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'user_id' + encodeURIComponent(userId)
    })
    .then(response => response.text())
    .then(data => {
        button.classList.add('btn-disable');
        alert(data);
    })
    .catch(error=>console.error('Error:', error));
}

function unRestrictUSer(button) {
    var userId = button.getAttribute('data-id');

    fetch('undo_restriction.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/x-www-form-urlencoded'
        },
        body: 'user_id' + encodeURIComponent(userId)
    })
    .then(response=>response.text())
    .then(data=>{
        button.classList.add('btn-unrestric');
        alert(data);
    })
    .catch(error=>console.error('Error', error));
}


document.addEventListener('DOMContentLoaded', function(){
    const modal = document.getElementById('restricModal');
    const restrictModal = document.querySelector('.btn-restrict');
    const closeButton = document.querySelector('.close-button1');

    restrictModal.addEventListener('click', function(){
        modal.style.display = 'block';
    });

    closeButton.addEventListener('click', function(){
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event){
        if (event.target === modal) {
            modal.style.display = 'none';
        }
        
    });
});

function restricOpen(userId) {
    const modal = document.getElementById('restricModal' + userId);
    const closeButton = modal.querySelector('.close-button1');

    modal.style.display = 'block';

    closeButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

function disableOpen(userId){
    const modal = document.getElementById('disableModal' + userId);
    const closeButton = document.querySelector('.close-button2');
    modal.style.display = 'block';

    closeButton.addEventListener('click', function(){
        modal.style.display = 'none';
    });
    
    window.addEventListener('click', function(event){
        if (event.target === modal){
            modal.style.display = 'none';
        }
    });
}

function openUnDisable(userId){
    const modal = document.getElementById('UndisableModal');
    const closeButton = document.querySelector('.close-button');
    modal.style.display = 'block';

    closeButton.addEventListener('click', function(){
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event){
        if (event.target === modal){
            modal.style.display = 'none';
        }
        
    });
}

