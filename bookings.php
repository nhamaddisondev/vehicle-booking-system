<?php
$pageTitle = "Book a Vehicle - Vehicle Booking System";
include 'includes/header.php';

// Success / Error message handling
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process booking data or redirect to your controller script inside bookings/
    $message = "Your booking request has been submitted successfully!";
}
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Book a Vehicle</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-success col-md-8 mx-auto"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <form action="bookings.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                                <option value="" selected disabled>Select a category</option>
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="van">Van / Minibus</option>
                                <option value="luxury">Luxury</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pickup_location" class="form-label">Pickup Location</label>
                            <input type="text" class="form-control" id="pickup_location" name="pickup_location" placeholder="City or airport" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="pickup_date" class="form-label">Pickup Date</label>
                            <input type="date" class="form-control" id="pickup_date" name="pickup_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="return_date" class="form-label">Return Date</label>
                            <input type="date" class="form-control" id="return_date" name="return_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Special Requests / Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional requirements..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Reservation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>