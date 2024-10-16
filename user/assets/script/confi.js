document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById("confirmationModal");
    var span = document.getElementsByClassName("close")[0];
    var confirmBtn = document.getElementById("confirmBtn");
    var cancelBtn = document.getElementById("cancelBtn");
    var selectedSlot = null;

    document.querySelectorAll('.box').forEach(function (box) {
        box.addEventListener('click', function () {
            selectedSlot = this.getAttribute('data-slot');
            document.getElementById("modalText").innerText = "You want to reserve this slot #" + selectedSlot + "?";
            modal.style.display = "block";
            location.reload(); 
        });
    });

    span.onclick = function () {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function () {
        modal.style.display = "none";
        location.reload();
    }

    confirmBtn.onclick = function () {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "reserve_slot.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var responseText = xhr.responseText
                alert(xhr.responseText);
                modal.style.display = "none";
                document.querySelector('.selected-slot').classList.remove('selected-slot');     
                document.querySelector('.box' + selectedSlot).classList.add('selected-slot');
                location.reload();
                

            }
        };
        xhr.send("slot_number=" + selectedSlot);
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
            
        }
    }
    
});


