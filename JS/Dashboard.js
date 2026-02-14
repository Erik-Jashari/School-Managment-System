// Homework Upload Logic
function setupHomeworkUpload() {
    // Handle file input changes
    const fileInputs = document.querySelectorAll('.file-input');
    fileInputs.forEach(fileInput => {
        fileInput.addEventListener('change', function() {
            const form = this.closest('.upload-form');
            if (form) {
                const fileNameSpan = form.querySelector('.file-name');
                const uploadBtn = form.querySelector('.upload-btn');
                if (this.files && this.files.length > 0) {
                    fileNameSpan.textContent = this.files[0].name;
                    uploadBtn.disabled = false;
                    console.log('File selected:', this.files[0].name);
                } else {
                    fileNameSpan.textContent = 'No file chosen';
                    uploadBtn.disabled = true;
                }
            }
        });
    });

    // Handle form submissions
    const forms = document.querySelectorAll('.upload-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const assignmentId = this.dataset.assignmentId;
            const fileInput = this.querySelector('.file-input');
            const uploadBtn = this.querySelector('.upload-btn');

            console.log('Form submitted with assignment ID:', assignmentId);

            if (!fileInput.files || !fileInput.files.length) {
                alert('Please choose a file first.');
                return;
            }

            // Validate file size (10MB)
            if (fileInput.files[0].size > 10 * 1024 * 1024) {
                alert('File size exceeds 10MB limit.');
                return;
            }

            const formData = new FormData();
            formData.append('homework_file', fileInput.files[0]);
            formData.append('assignment_id', assignmentId);

            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';

            console.log('Uploading file:', fileInput.files[0].name);

            fetch('includes/upload_homework.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                console.log('Upload response:', data);
                if (data.success) {
                    alert('File uploaded successfully!');
                    // Replace form with success message
                    const uploadDiv = form.closest('.assignment-upload');
                    uploadDiv.innerHTML = '<span class="already-submitted">✔ Submitted</span>';

                    // Update status badge
                    const card = uploadDiv.closest('.assignment-card');
                    const statusSpan = card.querySelector('.submission-status');
                    if (statusSpan) {
                        statusSpan.textContent = 'Submitted';
                        statusSpan.className = 'submission-status status-submitted';
                    }
                } else {
                    alert('Upload failed: ' + data.message);
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload';
                }
            })
            .catch(err => {
                console.error('Upload error:', err);
                alert('An error occurred while uploading. Please try again.');
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload';
            });
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setupHomeworkUpload();
});