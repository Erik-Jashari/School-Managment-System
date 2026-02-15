<?php
require_once __DIR__ . '/auth.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Require user to be logged in
requireLogin();

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];

// Only students can upload homework
if ($userRole !== 'Student') {
    echo json_encode(['success' => false, 'message' => 'Only students can upload homework']);
    exit;
}

// Validate assignment ID
$assignmentId = isset($_POST['assignment_id']) ? (int)$_POST['assignment_id'] : 0;
if ($assignmentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid assignment ID']);
    exit;
}

// Verify the assignment exists and the student belongs to the group
$stmt = $connection->prepare("
    SELECT a.AssignmentID, a.GroupID 
    FROM Assignments a
    JOIN Student_Groups sg ON a.GroupID = sg.GroupID
    WHERE a.AssignmentID = ? AND sg.UsersID = ?
");
$stmt->bind_param('ii', $assignmentId, $userId);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$assignment) {
    echo json_encode(['success' => false, 'message' => 'Assignment not found or you do not have access']);
    exit;
}

// Check if already submitted (and not just pending)
$stmt = $connection->prepare("SELECT SubmissionID, Status FROM Submissions WHERE AssignmentID = ? AND UsersID = ?");
$stmt->bind_param('ii', $assignmentId, $userId);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing && $existing['Status'] !== 'Pending') {
    echo json_encode(['success' => false, 'message' => 'You have already submitted this assignment']);
    exit;
}

// Validate file upload
if (!isset($_FILES['homework_file']) || $_FILES['homework_file']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Server configuration error',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
    ];
    $errorCode = isset($_FILES['homework_file']) ? $_FILES['homework_file']['error'] : UPLOAD_ERR_NO_FILE;
    $message = $errorMessages[$errorCode] ?? 'Unknown upload error';
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$file = $_FILES['homework_file'];

// Validate file size (max 10MB)
$maxSize = 10 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 10MB limit']);
    exit;
}

// Validate file type
$allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'pptx', 'xlsx'];
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($extension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'message' => 'File type not allowed. Accepted: ' . implode(', ', $allowedExtensions)]);
    exit;
}

// Create unique filename
$uploadsDir = __DIR__ . '/../uploads/submissions/';
$uniqueName = 'hw_' . $userId . '_' . $assignmentId . '_' . time() . '.' . $extension;
$filePath = $uploadsDir . $uniqueName;
$relativePath = 'uploads/submissions/' . $uniqueName;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
    exit;
}

// Insert or update submission record
if ($existing) {
    // Update existing pending submission
    $stmt = $connection->prepare("UPDATE Submissions SET FilePath = ?, Status = 'Submitted', SubmittedAt = NOW() WHERE SubmissionID = ?");
    $stmt->bind_param('si', $relativePath, $existing['SubmissionID']);
} else {
    // Insert new submission
    $stmt = $connection->prepare("INSERT INTO Submissions (AssignmentID, UsersID, FilePath, Status) VALUES (?, ?, ?, 'Submitted')");
    $stmt->bind_param('iis', $assignmentId, $userId, $relativePath);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Homework uploaded successfully!',
        'fileName' => basename($file['name']),
        'filePath' => $relativePath
    ]);
} else {
    // Clean up file on DB failure
    unlink($filePath);
    echo json_encode(['success' => false, 'message' => 'Failed to save submission record']);
}
$stmt->close();
