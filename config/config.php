<?php
session_start();

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$project_folder = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[1];
$base_url .= '/' . $project_folder;

//Database configuration
try {
    $host = 'localhost';
    $dbname = 'vehicle_booking_sys';
    $username = 'root';
    $password = '';

    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    define('APPURL', $base_url);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// User booking status
$user_booking_status = 'none' ; // you can set a default value for the booking status as "null or "none" or any other value that indicates no booking has been made yet.
if (isset($_SESSION['id'], $_SESSION['role']) && $_SESSION['role'] === 'user') {
    $user_id = $_SESSION['id'];
    $stmt = $conn->prepare("SELECT status FROM bookings WHERE user_id = :user_id ORDER BY id DESC LIMIT 1");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($booking) {
        $user_booking_status = $booking['status'];
    }
}