// Mock Data for Classes and Students
const MOCK_CLASS_DATA = {
    'Period 1: Algebra II': {
        totalStudents: 28,
        presentCount: 27,
        roster: [
            { id: '2024001', name: 'Liam Johnson', status: 'Active', grade: '92% (A)', missing: 'None', attendance: 'Present' },
            { id: '2024002', name: 'Emma Davis', status: 'Active', grade: '74% (C)', missing: '1 Assignment', attendance: 'Absent' },
            { id: '2024003', name: 'Noah Smith', status: 'At Risk', grade: '58% (F)', missing: '3 Assignments', attendance: 'Present' },
        ]
    },
    'Period 2: Geometry': {
        totalStudents: 32,
        presentCount: 32,
        roster: [
            { id: '2024101', name: 'Ava Brown', status: 'Active', grade: '98% (A)', missing: 'None', attendance: 'Present' },
            { id: '2024102', name: 'Jacob Wilson', status: 'Active', grade: '85% (B)', missing: 'None', attendance: 'Present' },
        ]
    },
    'Period 3: Calculus AP': {
        totalStudents: 15,
        presentCount: 15,
        roster: [
            { id: '2024201', name: 'Sophia Miller', status: 'Active', grade: '89% (B)', missing: 'None', attendance: 'Present' },
        ]
    },
    'Period 4: Planning': {
        totalStudents: 0,
        presentCount: 0,
        roster: [],
    },
    'Period 5: Algebra I': {
        totalStudents: 30,
        presentCount: 28,
        roster: [
            { id: '2024301', name: 'Ethan Jones', status: 'At Risk', grade: '62% (D)', missing: '2 Assignments', attendance: 'Present' },
            { id: '2024302', name: 'Mia Garcia', status: 'Active', grade: '88% (B)', missing: 'None', attendance: 'Present' },
            { id: '2024303', name: 'Oliver Rodriguez', status: 'Active', grade: '79% (C)', missing: '1 Assignment', attendance: 'Absent' },
        ]
    }
};

// 1. Period Selector / Class Switching Logic
function setupPeriodSelector() {
    const periodItems = document.querySelectorAll('.period-item');
    
    periodItems.forEach(item => {
        item.addEventListener('click', function() {
            // 1. Remove 'active' class from all periods
            periodItems.forEach(i => i.classList.remove('active'));
            
            // 2. Add 'active' class to the clicked period
            this.classList.add('active');
            
            // 3. Get the name of the selected class
            const selectedClass = this.textContent.trim();
            
            // 4. Load the corresponding mock data
            loadClassData(selectedClass);
        });
    });
    
    // Load the first period's data on initial page load
    const initialClass = document.querySelector('.period-item.active');
    if (initialClass) {
        loadClassData(initialClass.textContent.trim());
    }
}


function loadClassData(className) {
    const data = MOCK_CLASS_DATA[className];
    if (!data) return;

    // A. Update the main header
    document.querySelector('.roster-section h2').textContent = className + ' - Roster';

    // B. Update the Quick Stats
    updateStatsBar(data.totalStudents, data.presentCount);

    // C. Update the Student Roster Table
    updateRosterTable(data.roster);
    
    // D. Re-initialize the Attendance Toggles for the new table rows
    setupAttendanceToggle(data.totalStudents);
}


function updateStatsBar(total, present) {
    // Stat 1: Total Students
    document.querySelector('.stat-card:nth-child(1) p').textContent = total;
    
    // Stat 2: Attendance Rate
    const percentage = (total > 0) ? Math.round((present / total) * 100) : 100;
    document.querySelector('.stat-card:nth-child(2) p').textContent = `${percentage}%`;
    
    // Stat 3: Assignments Due
    document.querySelector('.stat-card:nth-child(3) p').textContent = '2'; 
}


// 2. Roster Table Generator Logic
function updateRosterTable(roster) {
    const tbody = document.querySelector('.student-table tbody');
    tbody.innerHTML = ''; // Clear existing rows

    roster.forEach(student => {
        const gradeClass = getGradeClass(student.grade);
        const row = document.createElement('tr');
        
        row.innerHTML = `
            <td>
                <strong>${student.name}</strong><br>
                <span style="font-size: 0.8rem; color: #888;">ID: ${student.id}</span>
            </td>
            <td><span style="color: ${student.status === 'At Risk' ? 'orange' : 'green'};">${student.status}</span></td>
            <td><button class="attendance-toggle ${student.attendance.toLowerCase()}" data-status="${student.attendance.toLowerCase()}">${student.attendance}</button></td>
            <td><span class="grade-badge ${gradeClass}">${student.grade}</span></td>
            <td><span style="color: ${student.missing !== 'None' ? 'red' : 'inherit'};">${student.missing}</span></td>
            <td><a href="#">Edit</a></td>
        `;
        tbody.appendChild(row);
    });
}

// Helper to assign a CSS class based on the letter grade
function getGradeClass(gradeString) {
    const gradeLetter = gradeString.match(/\(([A-Z])\)/)[1];
    switch(gradeLetter) {
        case 'A': return 'grade-a';
        case 'B': return 'grade-b';
        case 'C': return 'grade-c';
        case 'D': return 'grade-d';
        case 'F': return 'grade-f';
        default: return '';
    }
}


function setupAttendanceToggle(totalStudents) {
    const toggles = document.querySelectorAll('.attendance-toggle');
    const attendanceStatElement = document.querySelector('.stat-card:nth-child(2) p');
    
    // Get the current counts from the table for the newly loaded class
    let currentPresentCount = document.querySelectorAll('.attendance-toggle[data-status="present"]').length;

    toggles.forEach(button => {
        // Remove previous listeners to avoid conflicts when table reloads
        button.removeEventListener('click', handleAttendanceClick);
        button.addEventListener('click', handleAttendanceClick);
    });

    function handleAttendanceClick() {
        if (this.classList.contains('present')) {
            // Change status from Present to Absent
            this.classList.remove('present');
            this.classList.add('absent');
            this.textContent = 'Absent';
            this.dataset.status = 'absent';
            currentPresentCount--;
        } else {
            // Change status from Absent to Present
            this.classList.remove('absent');
            this.classList.add('present');
            this.textContent = 'Present';
            this.dataset.status = 'present';
            currentPresentCount++;
        }
        
        // Update the percentage stat at the top of the dashboard
        updateAttendanceStatDisplay(currentPresentCount, totalStudents);
    }
    
    function updateAttendanceStatDisplay(presentCount, total) {
        const percentage = (total > 0) ? Math.round((presentCount / total) * 100) : 100;
        attendanceStatElement.textContent = `${percentage}%`;
    }
}

document.addEventListener('DOMContentLoaded', () => {setupPeriodSelector(); 
});