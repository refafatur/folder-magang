document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const timeSlots = document.querySelectorAll('.time-slot-option');
    const form = document.querySelector('.modern-form');
    let loadingTimeouts = {};

    // Set minimum date to today
    dateInput.min = new Date().toISOString().split('T')[0];
    
    dateInput.addEventListener('change', function() {
        checkAllTimeSlots(this.value);
    });

    async function checkAllTimeSlots(date) {
        // Show loading state
        timeSlots.forEach(slot => {
            slot.classList.add('loading');
            slot.querySelector('input').disabled = true;
        });

        try {
            const promises = Array.from(timeSlots).map(async slot => {
                const time = slot.querySelector('input').value;
                const response = await fetch(`booking.php?check_availability=1&date=${date}&time=${time}`);
                if (!response.ok) throw new Error('Network error');
                return response.json();
            });

            const results = await Promise.all(promises);
            
            results.forEach((data, index) => {
                const slot = timeSlots[index];
                slot.classList.remove('loading');
                
                if (data.success) {
                    if (!data.available) {
                        slot.classList.add('disabled');
                        slot.querySelector('input').disabled = true;
                        // Add remaining slots info
                        slot.setAttribute('title', 'Fully booked');
                    } else {
                        slot.classList.remove('disabled');
                        slot.querySelector('input').disabled = false;
                        slot.setAttribute('title', `${data.remaining} slots available`);
                    }
                }
            });
        } catch (error) {
            console.error('Error checking availability:', error);
            // Show error message to user
            alert('Error checking availability. Please try again.');
        }
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const date = dateInput.value;
        const time = document.querySelector('input[name="time"]:checked')?.value;
        
        if (!date || !time) {
            alert('Please select both date and time');
            return;
        }

        // Additional validation here
        
        this.submit();
    });
});
