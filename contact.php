<?php
$pageTitle = "Contact Us - Vehicle Booking System";
include 'includes/header.php';

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process contact form query
    $successMsg = "Thank you for reaching out! We'll get back to you shortly.";
}
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Contact Us</h2>

    <?php if ($successMsg): ?>
        <div class="alert alert-success col-md-8 mx-auto"><?= htmlspecialchars($successMsg); ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <form action="contact.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>