<?php require '../config/config.php'; ?>


<?php require '../includes/header.php'; ?>

<?php

if (isset($_SESSION['username'])) {
    header("Location: " . APPURL . "");
    exit();
}

if (isset($_POST['submit'])) {
    if (!empty($_POST['email']) OR !empty($_POST['password'])) {
        echo "<script>alert('Please enter your email or password.')</script>";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $login = $conn->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
        $login->execute(['email' => $email, 'password' => $password]);
        $select = $login->fetch(PDO::FETCH_ASSOC);

        if ($login->rowCount() > 0) {
            if (password_verify($password, $select['password'])) {
                $_SESSION['username'] = $select['username'];
                $_SESSION['email'] = $select['email'];
                $_SESSION['id'] = $select['id'];
                header("Location: " . APPURL . "");
            } else {
                echo "<script>alert('Invalid email or password.')</script>";
            }

        }


    }
}

?>
<section class="relative bg-cover bg-center text-white"
    style="background-image: url('<?php echo $base_url; ?>/images/hero_1.jpg');" id="home-section">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative container mx-auto px-4 py-20">
        <div class="flex flex-wrap">
            <div class="w-full md:w-7/12">
                <h1 class="text-white font-bold text-4xl">Log In</h1>
                <div class="mt-2">
                    <a href="<?php echo $base_url; ?>" class="text-white/80 hover:text-white">Home</a>
                    <span class="mx-2">/</span>
                    <span class="text-white"><strong>Log In</strong></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap">
            <div class="w-full">
                <form action="login.php" method="POST" class="p-4 border rounded">

                    <div class="flex flex-wrap mb-3">
                        <div class="w-full">
                            <label class="text-black block mb-1" for="email">Email</label>
                            <input type="email" id="email" class="w-full px-3 py-2 border border-gray-300 rounded"
                                placeholder="Email address" name="email">
                        </div>
                    </div>

                    <div class="flex flex-wrap mb-4">
                        <div class="w-full">
                            <label class="text-black block mb-1" for="password">Password</label>
                            <input type="password" id="password" class="w-full px-3 py-2 border border-gray-300 rounded"
                                placeholder="Password" name="password">
                        </div>
                    </div>

                    <div class="flex flex-wrap">
                        <div class="w-full">
                            <input type="submit" name="submit" value="Log In"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded cursor-pointer">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</section>

<?php require '../includes/footer.php'; ?>