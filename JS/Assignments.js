document.addEventListener('DOMContentLoaded', function() {

    const createModal = document.getElementById("create-assignment-modal");
    const createBtn = document.querySelector(".create-assignment-btn");
    const createClose = document.querySelector("#create-assignment-modal .close-button");
    const createForm = document.getElementById("new-assignment-form");


    const gradeModal = document.getElementById("grade-submissions-modal");
    const gradeClose = document.querySelector(".grade-close-button");
    const gradeButtons = document.querySelectorAll(".grade-btn");
    const saveButton = document.querySelector(".save-grades-btn");

    // When the user clicks the CREATE button, open the modal 
    createBtn.onclick = function() {
        createModal.style.display = "block";
    }


    createClose.onclick = function() {
        createModal.style.display = "none";
        createForm.reset(); 
    }
    

    window.onclick = function(event) {
        if (event.target == createModal) {
            createModal.style.display = "none";
            createForm.reset(); 
        }
        // Close grading modal if clicked outside
        if (event.target == gradeModal) { 
            gradeModal.style.display = "none";
        }
    }
     form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // --- MOCK LOGIC: In a real app, this would be an API call ---
        const assignmentName = document.getElementById('assignment-name').value;
        const assignmentClass = document.getElementById('assignment-class').value;
        const dueDate = document.getElementById('due-date').value;
        
        console.log("New Assignment Created:", {
            name: assignmentName,
            class: assignmentClass,
            due: dueDate
        });

        alert(`Success! Assignment "${assignmentName}" published for ${assignmentClass}.`);

        modal.style.display = "none"; // Close the modal
        form.reset(); // Clear the form
    
        // Reload the page to reflect new assignment (for demo purposes)
        location.reload();
    });


    // 2. Assignment Grading Modal Logic


    gradeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // In a real app, we would fetch the assignment name/data from the row here, but for now we'll mock it untill the second part of the assignment.
            const assignmentRow = this.closest('tr');
            const assignmentName = assignmentRow.querySelector('td:first-child').textContent;
            
            // Update the modal title
            document.getElementById('grading-assignment-title').textContent = `Grading: ${assignmentName}`;
            
            // MOCK: Generate the table content based on the mock data (for demonstration)
            // Normally this data would be fetch this data based on the assignment ID
            // For now, we'll assume a static list of students for the first assignment
            // We already have some mock data, so we'll just show the modal.
            
            gradeModal.style.display = "block";
        });
    });

    // Close button for the grading modal
    gradeClose.onclick = function() {
        gradeModal.style.display = "none";
    }
    
    // MOCK: Save Grades Logic
    saveButton.addEventListener('click', function() {
        // Find all score inputs
        const scoreInputs = gradeModal.querySelectorAll('.score-input:not([disabled])');
        const grades = [];
        
        scoreInputs.forEach(input => {
            const row = input.closest('tr');
            grades.push({
                studentId: row.dataset.studentId,
                score: input.value
            });
        });
        
        console.log("Grades Saved:", grades);
        alert(`Successfully saved ${grades.length} grades for the assignment!`);
        
        gradeModal.style.display = "none"; // Close after saving
    });

});