document.addEventListener('DOMContentLoaded', function() {

    const createModal = document.getElementById("create-assignment-modal");
    const createBtn = document.querySelector(".create-assignment-btn");
    const createClose = document.querySelector("#create-assignment-modal .close-button");
    const createForm = document.getElementById("new-assignment-form");

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
    }
     form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // --- MOCK LOGIC:
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
    
        // Reload the page to reflect new assignment
        location.reload();
    });
});