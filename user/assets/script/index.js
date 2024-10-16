document.querySelector(".menuicn");
let nav = document.querySelector(".navcontainer");


menuicn.addEventListener("click", () => {
    nav.classList.toggle("navclose");

    let booking = document.getElementById('card.slot');
    booking.target.classList.add('hidden');
})

