document.addEventListener('DOMContentLoaded', function() {
    const userRole = '<?php echo $user_role; ?>'; 
    if (userRole === 'president' || userRole === 'vice_president') {
        return;
    }

    const timerElements = document.querySelectorAll('.timer');

    function initializeTimers() {
        timerElements.forEach(timer => {
            const slotNumber = timer.closest('.box').getAttribute('data-slot');
            let endTime = localStorage.getItem(`timer_end_${slotNumber}`);

            if (!endTime) {
                const startTime = Date.now();
                const timerDuration = 5 * 60 * 1000; // 5 minutes
                endTime = startTime + timerDuration;
                localStorage.setItem(`timer_end_${slotNumber}`, endTime);
            } else {
                endTime = parseInt(endTime, 10);
            }

            updateTimer(timer, endTime);
        });
    }

    function updateTimer(timer, endTime) {
        const interval = setInterval(() => {
            const now = Date.now();
            const timeDiff = endTime - now;

            if (timeDiff <= 0) {
                timer.innerText = 'Expired';
                timer.closest('.box').querySelector('.fa-car').classList.remove('reserved');
                timer.closest('.box').querySelector('.fa-car').classList.add('available');
                clearInterval(interval);

                const slotNumber = timer.closest('.box').getAttribute('data-slot');
                fetch('reserve_slot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'check_expiry': slotNumber
                    })
                })
                .then(response => response.text())
                .then(data => console.log(data))
                .catch(error => console.error('Error:', error));

                localStorage.removeItem(`timer_end_${slotNumber}`);
            } else {
                const minutes = Math.floor(timeDiff / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
                timer.innerText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            }
        }, 1000);
    }

    initializeTimers();
});
