function setupAttendanceToggle() {
    const toggles = document.querySelectorAll('.attendance-toggle');
    const attendanceStat = document.querySelector('.stat-card:nth-child(2) p');
    const totalStudents = 28; // Assuming 28 students based on the static data

    // Initial calculation (based on the HTML data)
    let initialAbsentCount = document.querySelectorAll('.attendance-toggle.absent').length;
    let initialPresentCount = totalStudents - initialAbsentCount;
    updateAttendanceStat(initialPresentCount, totalStudents);


    toggles.forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('present')) {
                // Change status from Present to Absent
                this.classList.remove('present');
                this.classList.add('absent');
                this.textContent = 'Absent';
                initialPresentCount--;
                
            } else {
                // Change status from Absent to Present
                this.classList.remove('absent');
                this.classList.add('present');
                this.textContent = 'Present';
                initialPresentCount++;
            }
            
            // Update the percentage stat at the top of the dashboard
            updateAttendanceStat(initialPresentCount, totalStudents);
        });
    });

    function updateAttendanceStat(presentCount, total) {
        const percentage = Math.round((presentCount / total) * 100);
        attendanceStat.textContent = `${percentage}%`;
    }
}

// Ensure this script runs after the DOM (and the header/footer) is loaded
document.addEventListener('DOMContentLoaded', setupAttendanceToggle);