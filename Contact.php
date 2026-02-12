<?php
session_start();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name !== '' && $email !== '' && $message !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $connection->prepare('INSERT INTO contact_messages (Name, Email, Message) VALUES (?, ?, ?)');
        if ($stmt) {
            $stmt->bind_param('sss', $name, $email, $message);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) {
                $_SESSION['flash_message'] = 'Thanks for your message! We will get back to you soon.';
                header('Location: Contact.php');
                exit;
            }
        }
    }

    $_SESSION['flash_message'] = 'Sorry, something went wrong. Please try again.';
    header('Location: Contact.php');
    exit;
}

$flashMessage = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link rel="stylesheet" href="CSS/Global.css">
    <link rel="stylesheet" href="CSS/Contact.css">
</head>
<body>
    <!-- Navigation Bar -->
    <div id="app-header"></div>

    <main class="dashboard-container"></main>

    <!-- Contact Section -->
    <div class="contact-container">
        <div id="bg-slider"></div>

        <div class="contact-left">
            <h1>Contact Us</h1>
            <p>If you have questions, suggestions, or need support—just send us a message,  
            We usually reply within 24 hours.</p>

            <?php if ($flashMessage !== ''): ?>
                <div class="contact-status"><?php echo htmlspecialchars($flashMessage, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form class="contact-form" action="Contact.php" method="post">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Your email" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Write your message..." required></textarea>

                <button type="submit" class="send-button">Send Message</button>
            </form>
        </div>

        <div class="contact-image">
            <img src="Images/feedback.png" alt="contact-image">
        </div>
    </div>

    <!-- Footer -->
    <div id="app-footer"></div>

    <script src="JS/HeaderFooter.js"></script>
    <script src="JS/Contact.js"></script>
</body>
</html>
