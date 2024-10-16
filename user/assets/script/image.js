function showFullImage(src) {
    var modal = document.getElementById("fullImageModal");
    var fullImage = document.getElementById("fullImage");
    fullImage.src = src;
    modal.style.display = "block";
}

function closeModal() {
    var modal = document.getElementById("fullImageModal");
    modal.style.display = "none"
}

