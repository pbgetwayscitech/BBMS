<?php
// auth/registerbank.php
// This page handles blood bank registration.

require_once '../../config/code_states.php';
require_once '../../config/functions.php';


session_start();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $bank_name = trim($_POST['bank_name'] ?? '');
    $bank_email = trim($_POST['bank_email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $state_id = trim($_POST['state_id'] ?? '');
    $bank_owner = trim($_POST['bank_owner'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone_number_m = trim($_POST['phone_number'] ?? '');

    // Basic validation
    if (
        empty($bank_name) || empty($bank_email) || empty($password) || empty($confirm_password) ||
        empty($pincode) || empty($state_id) || empty($bank_owner) || empty($address) || empty($phone_number_m)
    ) {
        $message = 'Please fill in all required fields.';
        $message_type = 'danger';
    } elseif (!filter_var($bank_email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format.';
        $message_type = 'danger';
    } elseif ($password !== $confirm_password) {
        $message = 'Passwords do not match.';
        $message_type = 'danger';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters long.';
        $message_type = 'danger';
    } elseif (!preg_match('/^[0-9]{6}$/', $pincode)) {
        $message = 'Pincode must be a 6-digit number.';
        $message_type = 'danger';
    } elseif (!preg_match('/^[0-9]{1,2}$/', $state_id) && $state_id < 1 || $state_id > 28) {
        $message = 'Please Select a valid State from list.';
        $message_type = 'danger';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone_number_m)) {
        $message = 'Phone number must be a 10-digit number.';
        $message_type = 'danger';
    } else {

        require_once "../../controller/admin_controller.php";

        if (does_admin_exist($bank_name, $bank_email, $phone_number_m)) {
            $message = 'A blood bank with the same name, email, or phone number already exists.';
            $message_type = 'danger';
        } else {

            require_once "../../model/bloodBank.php";

            $hashed_password = hashPassword($password);
            $bank_data = new BloodBank(
                null,
                $bank_name,
                $pincode,
                $state_id,
                $bank_owner,
                $address,
                $phone_number_m,
                $bank_email,
                $hashed_password,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0
            );

            if (register_blood_bank($bank_data)) {
                // Clear form fields
                $_POST = array();
                $message = 'Blood Bank registration successful!';
                $message_type = 'success';
            } else {
                $message = 'Blood Bank registration unsuccessful!';
                $message_type = 'danger';
            }
        }

    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Registration</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/registerbank.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons (for SVG icons) -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="flex flex-col min-h-screen">
    <header class="main-header">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="/index.php" class="flex items-center space-x-2 text-white text-2xl font-bold">
                <img src="/src/assets/logo.png" alt="Blood Bank Logo" class="w-8 h-8 rounded-full">
                <span>BBMS</span>
            </a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="/src/pages/search.php" class="main-nav-link">Search</a></li>
                    <li><a href="/src/pages/auth/registeruser.php" class="main-nav-link">Register Donor</a></li>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <li><a href="/src/pages/auth/logout.php" class="main-nav-link active">Logout</a></li>
                    <?php else: ?>
                        <li><a href="/src/pages/auth/login.php" class="main-nav-link active">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="flex justify-center items-center py-8">
            <div class="w-full max-w-2xl">
                <div class="card">
                    <h2 class="card-title text-center mb-6">Blood Bank Registration</h2>

                    <?php
                    // Display alert message using the simple direct HTML approach
                    if (!empty($message)) {
                        $alert_class = ($message_type === 'success') ? 'alert-success' : 'alert-danger';
                        echo "<div class='alert {$alert_class}' role='alert'>" . htmlEscape($message) . "</div>";
                    }
                    ?>

                    <div class='alert alert-danger' role='alert'>None of the below fields
                        can be updated/modified after successful registration. Please fill all the details carefully.
                    </div>

                    <form action="<?php echo htmlEscape($_SERVER['PHP_SELF']); ?>" method="post" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="bank_name" class="form-label">Blood Bank Name:</label>
                                <input type="text" class="form-input" id="bank_name" name="bank_name"
                                    value="<?php echo htmlEscape($_POST['bank_name'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="bank_email" class="form-label">Bank Email ID:</label>
                                <input type="email" class="form-input" id="bank_email" name="bank_email"
                                    value="<?php echo htmlEscape($_POST['bank_email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-input" id="password" name="password" required
                                    minlength="6">
                            </div>
                            <div>
                                <label for="confirm_password" class="form-label">Confirm Password:</label>
                                <input type="password" class="form-input" id="confirm_password" name="confirm_password"
                                    required minlength="6">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="pincode" class="form-label">Pincode:</label>
                                <input type="text" class="form-input" id="pincode" name="pincode"
                                    value="<?php echo htmlEscape($_POST['pincode'] ?? ''); ?>" required
                                    pattern="[0-9]{6}" title="Please enter a 6-digit pincode">
                            </div>
                            <div>
                                <label for="state_id" class="form-label">Select State</label>
                                <div class="col-md-6 mb-3">
                                    <select class="form-select form-input" id="state_id" name="state_id"
                                        value="<?php echo htmlEscape($_POST['state_id'] ?? ''); ?>" required
                                        pattern="[0-9]{1,2}" title="Please Select State">
                                        <option value="A">Select State</option>
                                        <?php
                                        foreach ($states as $key => $stateName) {
                                            $selected = (isset($_POST['state_id']) && $_POST['state_id'] == $key) ? 'selected' : '';
                                            echo "<option value='{$key}' {$selected}>{$stateName}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="bank_owner" class="form-label">Bank Owner Name:</label>
                                <input type="text" class="form-input" id="bank_owner" name="bank_owner"
                                    value="<?php echo htmlEscape($_POST['bank_owner'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div>
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-textarea" id="address" name="address" rows="3"
                                required><?php echo htmlEscape($_POST['address'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label for="phone_number" class="form-label">Phone Number:</label>
                            <input type="text" class="form-input" id="phone_number" name="phone_number"
                                value="<?php echo htmlEscape($_POST['phone_number'] ?? ''); ?>" required
                                pattern="[0-9]{10}" title="Please enter a 10-digit phone number">
                        </div>
                        <div class="flex justify-center pt-4">
                            <button type="submit" class="btn btn-primary w-full md:w-auto">
                                Register Blood Bank
                            </button>
                        </div>
                    </form>
                    <p class="text-center text-gray-600 mt-6">
                        Already have an account? <a href="login.php"
                            class="text-blue-600 hover:underline font-medium">Login here</a>.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer values  -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>Blood Bank Management System.</p>
            <p class="mt-2">Contact: +00 000 00000 |
                <a href="../src/pages/privacypolicy.php" class="text-blue-400 hover:underline mt-1 inline-block">Privacy
                    Policy</a>
            </p>
        </div>
    </footer>

    <!-- Script to initialize Lucide icons -->
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
