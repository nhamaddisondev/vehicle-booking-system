<?php require '../config/config.php'; ?>


<?php
if (isset($_SESSION['username'])) {
    header("location: " . APPURL . "");
}
if (isset($_POST['submit'])) {
    if (empty($_POST['username']) OR empty($_POST['email']) OR empty($_POST['password'])) {
        echo "<script>alert('Please fill in all fields.')</script>";
    } else {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $repassword = password_hash($_POST['repassword'], PASSWORD_DEFAULT);

        if ($password == $repassword) {

            if (strlen($email) > 22 OR strlen($username) > 15) {
                echo "<script>alert('Username or email too long.')</script>";
            }
            else{
                $insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
                $insert->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $password
                ]);
                header("location: " . APPURL . "/auth/login.php");
            }
        } else {
            echo "<script>alert('Passwords do not match.')</script>";
        }
    }
}
?>

<?php require "../includes/footer.php"; ?>