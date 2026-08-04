<?php require '../config/config.php';


$activeTab = 'loginRegister';
$driverOld = [];
$driverErrors = [];
$driverSHowStep2 = false;
if (isset($_SESSION['username'])) {
    header("location: " . APPURL . "");
    exit;
}

if (isset($_POST['driversubmit'])) {
    $activeTab = 'driver';
    $driverOld = $_POST;

    // Step 1: Account Credentials
    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['re-password'] ?? '';
    $img = 'driver_placeholder.jpg';
    $type = 'Driver';

    // Step 2: Vehicle & Licensing Info
    $license_no = strtoupper(trim($_POST['license_no'] ?? ''));
    $vehicle_type = trim($_POST['vehicle_type'] ?? '');
    $license_plate = strtoupper(trim($_POST['license_plate'] ?? ''));
    $vehicle_model = trim($_POST['vehicle_model'] ?? '');
    $seat_capacity = (int) ($_POST['seat_capacity'] ?? 0);

    $ALLOWED_VEHICLE_TYPES = ['Sedan', 'SUV', 'Van / Minibus', 'Bus', 'Pickup Truck', 'Motorcycle / Rickshaw'];
}
// Mandatory Field Label Checks
$labels = [
    'fullname' => 'Full Name / Driver Name',
    'username' => 'Username',
    'email' => 'Email Address',
    'contact' => 'Contact Number',
    'password' => 'Password',
    're-password' => 'Re-type Password',
    'license_no' => 'Driver License Number',
    'vehicle_type' => 'Vehicle Type',
    'license_plate' => 'License Plate Number',
    'vehicle_model' => 'Vehicle Brand & Model',
    'seat_capacity' => 'Passenger Seating Capacity'
];

foreach ($labels as $field => $label) {
    if (empty($_POST[$field])) {
        $driverErrors[$field] = "$label is required.";
    }
}

// Specific Field Validations
if (!isset($driverErrors['re-password']) && $password !== $repassword) {
    $driverErrors['re-password'] = "Passwords do not match.";
}
if (!isset($driverErrors['email']) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $driverErrors['email'] = "Please provide a valid email address.";
}
if (!isset($driverErrors['vehicle_type']) && !in_array($vehicle_type, $ALLOWED_VEHICLE_TYPES, true)) {
    $driverErrors['vehicle_type'] = 'Invalid vehicle type selected.';
}

// Database Uniqueness Checks via PDO Prepared Statements
if (!isset($driverErrors['email'])) {
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn())
        $driverErrors['email'] = 'This email is already registered.';
}
if (!isset($driverErrors['username'])) {
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE username = :u LIMIT 1");
    $stmt->execute([':u' => $username]);
    if ($stmt->fetchColumn())
        $driverErrors['username'] = 'This username is already taken.';
}
if (!isset($driverErrors['license_no'])) {
    $stmt = $conn->prepare("SELECT 1 FROM driver_details WHERE license_no = :lic LIMIT 1");
    $stmt->execute([':lic' => $license_no]);
    if ($stmt->fetchColumn())
        $driverErrors['license_no'] = 'This license number is already registered.';
}
if (!isset($driverErrors['license_plate'])) {
    $stmt = $conn->prepare("SELECT 1 FROM driver_details WHERE license_plate = :plate LIMIT 1");
    $stmt->execute([':plate' => $license_plate]);
    if ($stmt->fetchColumn())
        $driverErrors['license_plate'] = 'This vehicle license plate is already registered.';
}

// Determine Step Focus
$step1Keys = ['fullname', 'username', 'email', 'contact', 'password', 're-password'];
$driverShowStep2 = true;
foreach ($step1Keys as $k) {
    if (isset($driverErrors[$k])) {
        $driverShowStep2 = false;
        break;
    }
}

// Execution Transaction
if (empty($driverErrors)) {
    try {
        $conn->beginTransaction();

        $insertUser = $conn->prepare("
                INSERT INTO users (fullname, username, email, contact, mypassword, img, type)
                VALUES (:fullname, :username, :email, :contact, :mypassword, :img, :type)
            ");
        $insertUser->execute([
            ':fullname' => $fullname,
            ':username' => $username,
            ':email' => $email,
            ':contact' => $contact,
            ':mypassword' => password_hash($password, PASSWORD_DEFAULT),
            ':img' => $img,
            ':type' => $type
        ]);
        $userId = (int) $conn->lastInsertId();

        $insertDriver = $conn->prepare("
                INSERT INTO driver_details 
                  (user_id, license_no, vehicle_type, license_plate, vehicle_model, seat_capacity)
                VALUES 
                  (:user_id, :license_no, :vehicle_type, :license_plate, :vehicle_model, :seat_capacity)
            ");
        $insertDriver->execute([
            ':user_id' => $userId,
            ':license_no' => $license_no,
            ':vehicle_type' => $vehicle_type,
            ':license_plate' => $license_plate,
            ':vehicle_model' => $vehicle_model,
            ':seat_capacity' => $seat_capacity
        ]);

        $conn->commit();
        $_SESSION['successMsg'] = "Driver account created successfully! You can now log in.";
        header("location: loginRegister.php");
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        $driverErrors['_form'] = "Unable to create account right now. Please try again.";
        $driverShowStep2 = true;
    }
}

/* =========================================================================
   2. CUSTOMER / PASSENGER REGISTER
   ========================================================================= */
$customerError = "";
if (isset($_POST['customersubmit'])) {
    $activeTab = 'customer';

    $fullname = trim($_POST['fullname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $password = $_POST['password'] ?? '';
    $repassword = $_POST['re-password'] ?? '';
    $img = 'customer_placeholder.jpg';
    $type = 'Customer';

    if (empty($username) || empty($email) || empty($password) || empty($repassword) || empty($fullname)) {
        $customerError = "Please fill in all required fields.";
    } elseif ($password !== $repassword) {
        $customerError = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $customerError = "Please enter a valid email address.";
    } else {
        // Uniqueness check using Prepared Statement
        $validate = $conn->prepare("SELECT 1 FROM users WHERE email = :email OR username = :username LIMIT 1");
        $validate->execute([':email' => $email, ':username' => $username]);

        if ($validate->fetchColumn()) {
            $customerError = "Email or Username is already registered.";
        } else {
            $insert = $conn->prepare("
                INSERT INTO users (fullname, username, email, contact, mypassword, img, type)
                VALUES (:fullname, :username, :email, :contact, :mypassword, :img, :type)
            ");
            $insert->execute([
                ':fullname' => $fullname,
                ':username' => $username,
                ':email' => $email,
                ':contact' => $contact,
                ':mypassword' => password_hash($password, PASSWORD_DEFAULT),
                ':img' => $img,
                ':type' => $type,
            ]);

            $_SESSION['successMsg'] = "Your customer account has been created. Please log in.";
            header("location: loginRegister.php");
            exit;
        }
    }
}

/* =========================================================================
   3. LOGIN CONTROLLER
   ========================================================================= */
$loginError = "";
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $loginError = "Please enter both Email and Password.";
    } else {
        $login = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $login->execute([':email' => $email]);
        $user = $login->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mypassword'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['type'] = $user['type'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['contact'] = $user['contact'];
            $_SESSION['image'] = $user['img'];

            header("location: " . APPURL . "");
            exit;
        } else {
            $loginError = "Invalid email or password credentials.";
        }
    }
}

require "../includes/header.php";
?>

<!-- HERO SECTION -->
<section class="relative bg-slate-900 py-16 text-white bg-cover bg-center" style="background-image: url('../images/tst.jpg');" id="home-section">
  <!-- Dark overlay -->
  <div class="absolute inset-0 bg-black/60"></div>
  
  <div class="relative container mx-auto px-4 text-center">
    <span class="text-xs uppercase tracking-widest text-indigo-400 font-semibold mb-1 block">Account</span>
    <h2 class="text-3xl md:text-4xl font-extrabold mb-2">Welcome Back</h2>
    <p class="text-slate-300 text-sm md:text-base max-w-md mx-auto">
      Sign in or create an account — for Passengers & Vehicle Partners
    </p>
  </div>
</section>

<!-- MAIN AUTH SECTION -->
<section class="py-12 bg-slate-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-2xl">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
      
      <!-- TABS HEADER -->
      <ul class="flex border-b border-slate-200 bg-slate-100/50 p-1.5 gap-1" id="authTabs" role="tablist">
        <!-- LOGIN TAB -->
        <li class="flex-1 text-center">
          <a class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl font-medium text-sm transition-all duration-200 <?php echo ($activeTab==='login' ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'); ?>" 
             id="tab-login" data-toggle="pill" href="#pills-login" role="tab" aria-selected="<?php echo ($activeTab==='login'?'true':'false'); ?>">
            <i class="fa fa-sign-in text-base"></i>
            <span>Login</span>
          </a>
        </li>

        <!-- REGISTER PASSENGER TAB -->
        <li class="flex-1 text-center">
          <a class="flex items-center justify-center gap-1.5 py-3 px-2 rounded-xl font-medium text-sm transition-all duration-200 <?php echo ($activeTab==='js' ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'); ?>" 
             id="tab-js" data-toggle="pill" href="#pills-js" role="tab" aria-selected="<?php echo ($activeTab==='js'?'true':'false'); ?>">
            <i class="fa fa-user-plus text-base"></i>
            <span class="flex flex-col sm:flex-row sm:gap-1 items-center">
              <span>Register</span>
              <span class="text-xs opacity-75 font-normal">(Passenger)</span>
            </span>
          </a>
        </li>

        <!-- REGISTER DRIVER TAB -->
        <li class="flex-1 text-center">
          <a class="flex items-center justify-center gap-1.5 py-3 px-2 rounded-xl font-medium text-sm transition-all duration-200 <?php echo ($activeTab==='emp' ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50'); ?>" 
             id="tab-emp" data-toggle="pill" href="#pills-emp" role="tab" aria-selected="<?php echo ($activeTab==='emp'?'true':'false'); ?>">
            <i class="fa fa-car text-base"></i>
            <span class="flex flex-col sm:flex-row sm:gap-1 items-center">
              <span>Register</span>
              <span class="text-xs opacity-75 font-normal">(Driver/Owner)</span>
            </span>
          </a>
        </li>
      </ul>

      <!-- AUTH BODY -->
      <div class="p-6 md:p-8">
        <div class="tab-content" id="pills-tabContent">

          <!-- ==================== 1. LOGIN TAB ==================== -->
          <div class="tab-pane <?php echo ($activeTab==='login' ? 'block' : 'hidden'); ?>" id="pills-login" role="tabpanel">
            
            <?php if (!empty($_SESSION['successMsg'])): ?>
              <div class="mb-5 flex items-center justify-between p-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200" role="alert">
                <div class="flex items-center gap-2">
                  <i class="fa fa-check-circle text-emerald-600"></i>
                  <span><?= htmlspecialchars($_SESSION['successMsg']); ?></span>
                </div>
                <button type="button" class="text-emerald-500 hover:text-emerald-700 font-bold ml-3" onclick="this.parentElement.remove();">&times;</button>
              </div>
              <?php unset($_SESSION['successMsg']); ?>
            <?php endif; ?>

            <div class="text-center mb-6">
              <p class="text-slate-500 text-sm">Sign in with your registered Email & Password</p>

              <?php if (!empty($error)): ?>
                <div class="mt-3 flex items-center justify-between p-3.5 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200" role="alert">
                  <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                  <button type="button" class="text-red-400 hover:text-red-600 font-bold ml-2" onclick="this.parentElement.remove();">&times;</button>
                </div>
              <?php endif; ?>
            </div>

            <form action="loginRegister.php" method="POST" novalidate class="space-y-4">
              <div>
                <label for="loginEmail" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                <div class="relative rounded-lg shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa fa-envelope"></i>
                  </div>
                  <input type="email" id="loginEmail" name="email" required placeholder="you@example.com"
                         class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                </div>
              </div>

              <div>
                <label for="loginPassword" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <div class="relative rounded-lg shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa fa-lock"></i>
                  </div>
                  <input type="password" id="loginPassword" name="password" required placeholder="••••••••"
                         class="block w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                  <button class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" type="button" data-toggle="password" data-target="#loginPassword">
                    <i class="far fa-eye"></i>
                  </button>
                </div>
              </div>

              <button class="w-full mt-2 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-lg shadow-md hover:shadow-indigo-200 transition-all text-sm" type="submit" name="login">
                Log in
              </button>
            </form>
          </div>

          <!-- ==================== 2. REGISTER PASSENGER ==================== -->
          <div class="tab-pane <?php echo ($activeTab==='js' ? 'block' : 'hidden'); ?>" id="pills-js" role="tabpanel">
            <div class="text-center mb-6">
              <p class="text-slate-500 text-sm">Create your free Passenger Account</p>
            </div>

            <form action="loginRegister.php" method="POST" novalidate class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                <input type="text" name="fullname" required placeholder="John Doe"
                       class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                  <input type="text" name="username" required placeholder="@username"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                  <input type="email" name="email" required placeholder="passenger@example.com"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone / Contact Number</label>
                <input type="tel" name="contact" required placeholder="+1 234 567 890"
                       class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">City / Region</label>
                  <select name="region_id" required class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <option value="">Select city/region</option>
                    <?php foreach ($regionRows as $rg): ?>
                      <option value="<?= (int)$rg['id'] ?>"><?= htmlspecialchars($rg['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Pickup Address / Location</label>
                  <input type="text" name="address" required placeholder="123 Main Street"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                </div>
              </div>

              <input type="hidden" value="Passenger" name="type">

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                  <input type="password" name="password" required placeholder="Password"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Re-type Password</label>
                  <input type="password" name="re-password" required placeholder="Re-type Password"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                </div>
              </div>

              <button class="w-full mt-4 py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-lg shadow-md hover:shadow-indigo-200 transition-all text-sm" type="submit" name="submit">
                Create Passenger Account
              </button>
            </form>
          </div>

          <!-- ==================== 3. REGISTER DRIVER / VEHICLE OWNER (WIZARD) ==================== -->
          <div class="tab-pane <?php echo ($activeTab==='emp' ? 'block' : 'hidden'); ?>" id="pills-emp" role="tabpanel">
            
            <?php if(!empty($empErrors['_form'])): ?>
              <div class="mb-4 p-3.5 text-sm text-red-700 bg-red-50 rounded-xl border border-red-200">
                <?php echo htmlspecialchars($empErrors['_form']); ?>
              </div>
            <?php endif; ?>

            <div class="text-center mb-2">
              <p class="text-slate-500 text-sm">Become a Vehicle Partner / Driver</p>
            </div>

            <!-- STEP INDICATORS -->
            <div class="flex items-center justify-center gap-2 mb-6">
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold transition-colors <?php echo ($activeTab==='emp' && $empShowStep2) ? 'bg-slate-200 text-slate-700' : 'bg-indigo-600 text-white'; ?>" id="empStep1Dot">1</span>
              <span class="text-xs text-slate-500 font-medium">Account Details</span>
              <span class="text-slate-400 text-xs">→</span>
              <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold transition-colors <?php echo ($activeTab==='emp' && $empShowStep2) ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700'; ?>" id="empStep2Dot">2</span>
              <span class="text-xs text-slate-500 font-medium">Vehicle & License</span>
            </div>

            <form id="empForm" action="loginRegister.php" method="POST" novalidate>
              <input type="hidden" name="type" value="Driver">

              <!-- STEP 1 CONTAINER -->
              <div id="driverStep1" class="space-y-4 <?php echo ($activeTab==='emp' && $empShowStep2 ? 'hidden' : 'block'); ?>">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Full Name / Driver Name</label>
                  <input type="text" name="fullname" placeholder="Name of Driver or Fleet Operator"
                         value="<?php echo htmlspecialchars($empOld['fullname'] ?? ''); ?>"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                  <?php if(!empty($empErrors['fullname'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['fullname']; ?></span><?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" placeholder="@drivername" maxlength="30"
                           value="<?php echo htmlspecialchars($empOld['username'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['username'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['username']; ?></span><?php endif; ?>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Official Email</label>
                    <input type="email" name="email" placeholder="driver@example.com" maxlength="120"
                           value="<?php echo htmlspecialchars($empOld['email'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['email'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['email']; ?></span><?php endif; ?>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mobile / WhatsApp Number</label>
                    <input type="text" name="contact" placeholder="+1 234 567 890"
                           value="<?php echo htmlspecialchars($empOld['contact'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['contact'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['contact']; ?></span><?php endif; ?>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" placeholder="Password" minlength="6"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['password'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['password']; ?></span><?php endif; ?>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Re-type Password</label>
                  <input type="password" name="re-password" placeholder="Re-type password" minlength="6"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                  <?php if(!empty($empErrors['re-password'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['re-password']; ?></span><?php endif; ?>
                </div>

                <div class="flex justify-end pt-2">
                  <button type="button" id="empNext" class="py-2.5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm text-sm transition-all">
                    Next Step →
                  </button>
                </div>
              </div>

              <!-- STEP 2 CONTAINER -->
              <div id="driverStep2" class="space-y-4 <?php echo ($activeTab==='emp' && $empShowStep2 ? 'block' : 'hidden'); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Driver's License No.</label>
                    <input type="text" name="company_website" placeholder="DL-9988776655"
                           value="<?php echo htmlspecialchars($empOld['company_website'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['company_website'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['company_website']; ?></span><?php endif; ?>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Vehicle Category</label>
                    <input type="text" name="industry" placeholder="e.g. Sedan, SUV, Van, TukTuk"
                           value="<?php echo htmlspecialchars($empOld['industry'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['industry'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['industry']; ?></span><?php endif; ?>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Garage / Base Address</label>
                    <input type="text" name="address_line" placeholder="Street, City, Province"
                           value="<?php echo htmlspecialchars($empOld['address_line'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['address_line'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['address_line']; ?></span><?php endif; ?>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Postal Code</label>
                    <input type="text" name="postal_code" placeholder="12000"
                           value="<?php echo htmlspecialchars($empOld['postal_code'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['postal_code'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['postal_code']; ?></span><?php endif; ?>
                  </div>
                </div>

                <!-- Vehicle Registration / Plate Number -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Vehicle Registration / Plate Number <span class="text-red-500">*</span></label>
                  <input type="text" name="business_reg_no" required placeholder="ABC-1234" pattern="[A-Za-z0-9\/-]{4,20}" maxlength="20"
                         value="<?php echo htmlspecialchars($empOld['business_reg_no'] ?? ''); ?>"
                         class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                  <p class="text-xs text-slate-500 mt-1">Enter your vehicle plate or transport operator license number.</p>
                  <?php if(!empty($empErrors['business_reg_no'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['business_reg_no']; ?></span><?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Passenger Capacity -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Vehicle Passenger Capacity</label>
                    <?php $cs = $empOld['company_size'] ?? ''; ?>
                    <select name="company_size" class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                      <option value="">Select seating capacity...</option>
                      <option <?php echo ($cs==='1–4' ? 'selected' : ''); ?>>1–4 Seats (Standard Car)</option>
                      <option <?php echo ($cs==='5–7' ? 'selected' : ''); ?>>5–7 Seats (SUV/Minivan)</option>
                      <option <?php echo ($cs==='8–15' ? 'selected' : ''); ?>>8–15 Seats (Van/Minibus)</option>
                      <option <?php echo ($cs==='16+' ? 'selected' : ''); ?>>16+ Seats (Coach Bus)</option>
                    </select>
                    <?php if(!empty($empErrors['company_size'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['company_size']; ?></span><?php endif; ?>
                  </div>

                  <!-- Partner Type -->
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Partnership Type</label>
                    <?php $ot = $empOld['org_type'] ?? ''; ?>
                    <select name="org_type" id="orgType" class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                      <option value="">Select...</option>
                      <?php
                        $orgs = ['Individual Driver','Independent Taxi Owner','Fleet Operator','Rental Agency','Corporate Transport'];
                        foreach($orgs as $o){
                          $sel = ($ot===$o)?'selected':'';
                          echo "<option $sel>".htmlspecialchars($o)."</option>";
                        }
                      ?>
                    </select>
                    <p id="orgHelp" class="text-xs text-slate-500 mt-1" style="<?php echo ($ot==='Individual Driver'?'display:block':'display:none'); ?>;">
                      Individual drivers must keep their driver license and vehicle insurance documents updated.
                    </p>
                    <?php if(!empty($empErrors['org_type'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['org_type']; ?></span><?php endif; ?>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Vehicle Model Year</label>
                    <input type="number" name="established_year" min="1900" max="2099" step="1" placeholder="2022"
                           value="<?php echo htmlspecialchars($empOld['established_year'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['established_year'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['established_year']; ?></span><?php endif; ?>
                  </div>
                  <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Working / Service Hours</label>
                    <input type="text" name="operating_hours" placeholder="Mon–Sun 06:00–22:00 or 24/7"
                           value="<?php echo htmlspecialchars($empOld['operating_hours'] ?? ''); ?>"
                           class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all outline-none">
                    <?php if(!empty($empErrors['operating_hours'])): ?><span class="text-xs text-red-600 mt-1 block"><?php echo $empErrors['operating_hours']; ?></span><?php endif; ?>
                  </div>
                </div>

                <div class="flex justify-between items-center pt-2">
                  <button type="button" id="empBack" class="py-2.5 px-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-sm transition-all">
                    ← Back
                  </button>
                  <button type="submit" name="employersubmit" class="py-2.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md transition-all text-sm">
                    Complete Registration
                  </button>
                </div>
              </div>

            </form>
          </div>

        </div><!-- /tab-content -->
      </div><!-- /auth-body -->
    </div><!-- /card -->
  </div>
</section>

<!-- JAVASCRIPT FOR TABS & WIZARD -->
<script>
(function(){
  // Password visibility toggle
  document.querySelectorAll('[data-toggle="password"]').forEach(function(btn){
    var targetSel = btn.getAttribute('data-target');
    var target = document.querySelector(targetSel);
    if(!target) return;
    btn.addEventListener('click', function(){
      var isPwd = target.type === 'password';
      target.type = isPwd ? 'text' : 'password';
      var icon = btn.querySelector('i');
      if(icon){
        icon.classList.toggle('fa-eye', !isPwd);
        icon.classList.toggle('fa-eye-slash', isPwd);
      }
    });
  });

  // Simple Tab Switcher
  var tabLinks = document.querySelectorAll('#authTabs a[data-toggle="pill"]');
  tabLinks.forEach(function(tab){
    tab.addEventListener('click', function(e){
      e.preventDefault();
      var targetId = this.getAttribute('href');
      
      // Update Tab Styles
      tabLinks.forEach(function(link){
        link.className = 'flex items-center justify-center gap-2 py-3 px-3 rounded-xl font-medium text-sm transition-all duration-200 text-slate-600 hover:text-slate-900 hover:bg-white/50';
      });
      this.className = 'flex items-center justify-center gap-2 py-3 px-3 rounded-xl font-semibold text-sm transition-all duration-200 bg-white text-indigo-600 shadow-sm';

      // Hide all panes
      document.querySelectorAll('.tab-pane').forEach(function(pane){
        pane.classList.add('hidden');
        pane.classList.remove('block');
      });

      // Show selected pane
      var activePane = document.querySelector(targetId);
      if(activePane){
        activePane.classList.remove('hidden');
        activePane.classList.add('block');
      }
    });
  });

  // Driver Wizard Logic
  var nextBtn = document.getElementById('empNext');
  var backBtn = document.getElementById('empBack');
  var step1 = document.getElementById('driverStep1');
  var step2 = document.getElementById('driverStep2');
  var dot1 = document.getElementById('empStep1Dot');
  var dot2 = document.getElementById('empStep2Dot');
  var form = document.getElementById('empForm');

  function setStep(s){
    if(s === 2){
      step1.classList.add('hidden');
      step1.classList.remove('block');
      step2.classList.remove('hidden');
      step2.classList.add('block');

      dot1.className = 'inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-slate-200 text-slate-700';
      dot2.className = 'inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-indigo-600 text-white';
    } else {
      step2.classList.add('hidden');
      step2.classList.remove('block');
      step1.classList.remove('hidden');
      step1.classList.add('block');

      dot1.className = 'inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-indigo-600 text-white';
      dot2.className = 'inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-slate-200 text-slate-700';
    }
  }

  function validStep1() {
    var req = ['fullname','username','email','contact','password','re-password'];
    for (var i=0; i<req.length; i++){
      var el = form.querySelector('[name="'+req[i]+'"]');
      if(!el || !el.value.trim()){ el && el.focus(); return false; }
    }
    var p1 = form.querySelector('[name="password"]').value.trim();
    var p2 = form.querySelector('[name="re-password"]').value.trim();
    if (p1 !== p2){
      alert('Passwords do not match');
      form.querySelector('[name="re-password"]').focus();
      return false;
    }
    return true;
  }

  if(nextBtn){ nextBtn.addEventListener('click', function(){ if(validStep1()) setStep(2); }); }
  if(backBtn){ backBtn.addEventListener('click', function(){ setStep(1); }); }
})();

(function(){
  var orgSel = document.getElementById('orgType');
  var orgHelp = document.getElementById('orgHelp');
  if (orgSel && orgHelp) {
    orgSel.addEventListener('change', function(){
      orgHelp.style.display = (orgSel.value === 'Individual Driver') ? 'block' : 'none';
    });
  }
})();
</script>

<?php require "../includes/footer.php"; ?>