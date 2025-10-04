<?php
// auth/registeruser.php
// This page handles user registration.
session_start();

// file that defines common values
require_once "../../config/code_states.php";
require_once "../../config/code_genders.php";
require_once "../../config/code_bloodgroups.php";

// file that defines some common functoins
require_once "../../config/functions.php";


$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize and validate inputs
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fathers_name = trim($_POST['fathers_name'] ?? '');
    $mothers_name = trim($_POST['mothers_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $state_id = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $blood_group = trim($_POST['blood_group'] ?? '');
    $disease = trim($_POST['disease'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmpassword = $_POST['confirmpassword'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $unique_id = $_POST['unique_id'] ?? '';

    // Basic server-side validation
    if (
        empty($fullname) || empty($email) || empty($fathers_name) || empty($mothers_name) ||
        empty($address) || empty($phone_number) || empty($pincode) || empty($gender) ||
        empty($blood_group) || empty($disease) || empty($notes) ||
        empty($password) || empty($confirmpassword) || empty($dob) || empty($unique_id)
    ) {
        $message = "Please fill in all required fields.";
        $message_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "danger";
    } elseif (strlen($phone_number) != 10 || !is_numeric($phone_number)) {
        $message = "Phone number must be a 10-digit number.";
        $message_type = "danger";
    } elseif ($state_id <= 0 || $state_id > 28) {
        $message = "Please select a valid state.";
        $message_type = "danger";
    } elseif (strlen($pincode) != 6 || !is_numeric($pincode)) {
        $message = "Pincode must be a 6-digit number.";
        $message_type = "danger";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
        $message_type = "danger";
    } elseif ($password !== $confirmpassword) {
        $message = "Passwords do not match.";
        $message_type = "danger";
    } elseif (!in_array($gender, array_keys($genders))) {
        $message = "Please select a valid gender.";
        $message_type = "danger";
    } elseif (!in_array($blood_group, array_keys($blood_groups))) {
        $message = "Please select a valid blood group.";
        $message_type = "danger";
    } else {

        require_once "../../controller/user_controller.php";

        // Check if user already exists
        if (does_user_exist($email, $phone_number)) {
            $message = "A user with this email or phone number already exists.";
            $message_type = "danger";
        } else {
            require_once "../../model/genDonor.php";

            // Hash the password and prepare to insert new user
            $password_hash = hashPassword($password);

            //Prepare genDonor Class
            $newUser = new GenDonor(
                null,
                $fullname,
                $email,
                $fathers_name,
                $mothers_name,
                $dob,
                $address,
                $phone_number,
                $state_id,
                $pincode,
                $gender,
                $blood_group,
                $disease,
                $notes,
                $unique_id,
                $password_hash
            );

            if (register_user($newUser)) {
                // Registration successful

                // Clear form fields
                $_POST = array();

                // You can show a success message or redirect to a login page
                header("Location: /src/pages/auth/login.php?registration=success");
                exit;

            } else {
                $message = "Database error during registration.";
                $message_type = "danger";
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
    <title>User Registration</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/registeruser.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    <li><a href="/src/pages/auth/registerbank.php" class="main-nav-link">Register Bank</a></li>
                    <li><a href="/src/pages/auth/login.php" class="main-nav-link">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="flex justify-center items-center py-8">
            <div class="w-full max-w-2xl">
                <div class="card">
                    <h2 class="card-title text-center mb-6">New User Registration</h2>

                    <?php
                    if (!empty($message)) {
                        $alert_class = ($message_type === 'success') ? 'alert-success' : 'alert-danger';
                        echo "<div class='alert {$alert_class}' role='alert'>" . htmlEscape($message) . "</div>";
                    }
                    ?>

                    <div class='alert alert-danger' role='alert'>Note : Email-ID, Phone Number, Unique-ID and Passowrd
                        can't be updated/modified after successful registration. Please Fill all the details Carefully.
                    </div>

                    <form action="<?php echo htmlEscape($_SERVER['PHP_SELF']); ?>" method="post" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="fullname" class="form-label">Full Name:</label>
                                <input type="text" class="form-input" id="fullname" name="fullname"
                                    value="<?php echo htmlEscape($_POST['fullname'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="email" class="form-label">Email Id:</label>
                                <input type="email" class="form-input" id="email" name="email"
                                    value="<?php echo htmlEscape($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="fathers_name" class="form-label">Father's Name:</label>
                                <input type="text" class="form-input" id="fathers_name" name="fathers_name"
                                    value="<?php echo htmlEscape($_POST['fathers_name'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="mothers_name" class="form-label">Mother's Name:</label>
                                <input type="text" class="form-input" id="mothers_name" name="mothers_name"
                                    value="<?php echo htmlEscape($_POST['mothers_name'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="dob" class="form-label">Date of Birth:</label>
                                <input type="date" class="form-input w-full" id="dob" name="dob"
                                    value="<?php echo htmlEscape($_POST['dob'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="unique_id" class="form-label">Unique ID (AADHAR):</label>
                                <input type="number" class="form-input" id="unique_id" name="unique_id"
                                    value="<?php echo htmlEscape($_POST['unique_id'] ?? ''); ?>" required
                                    pattern="[0-9]">
                            </div>
                        </div>


                        <div>
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-textarea" id="address" name="address" rows="3"
                                required><?php echo htmlEscape($_POST['address'] ?? ''); ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="phone_number" class="form-label">Phone Number:</label>
                                <input type="tel" class="form-input" id="phone_number" name="phone_number"
                                    value="<?php echo htmlEscape($_POST['phone_number'] ?? ''); ?>" required
                                    pattern="[0-9]{10}">
                            </div>
                            <div>
                                <label for="state" class="form-label">State:</label>
                                <select class="form-select form-input" name="state" id="state" required
                                    pattern="[0-9]{1,2}">
                                    <option value="0">Select State</option>
                                    <?php
                                    foreach ($states as $key => $stateName) {
                                        $selected = (isset($_POST['state']) && $_POST['state'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$stateName}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="pincode" class="form-label">PIN CODE:</label>
                                <input type="tel" class="form-input" name="pincode" id="pincode"
                                    value="<?php echo htmlEscape($_POST['pincode'] ?? ''); ?>" required
                                    pattern="[0-9]{6}">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="form-label">Password: (min 8 characters)</label>
                                <input type="password" class="form-input" name="password" id="password" required
                                    minlength="8">
                            </div>
                            <div>
                                <label for="confirmpassword" class="form-label">Confirm Password:</label>
                                <input type="password" class="form-input" id="confirmpassword" name="confirmpassword"
                                    required minlength="8">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="gender" class="form-label">Gender:</label>
                                <select class="form-select form-input" name="gender" id="gender" required
                                    pattern="[0-9]{1}">
                                    <option value="A" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'A') ? 'selected' : ''; ?>>
                                        Select Gender</option>
                                    <?php
                                    foreach ($genders as $key => $gender) {
                                        $selected = (isset($_POST['gender']) && $_POST['gender'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$gender}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="blood_group" class="form-label">Blood Group:</label>
                                <select class="form-select form-input" name="blood_group" id="blood_group" required
                                    pattern="[0-9]{1}">
                                    <option value="A" <?php echo (isset($_POST['blood_group']) && $_POST['blood_group'] == 'A') ? 'selected' : ''; ?>>
                                        Select Blood Group</option>
                                    <?php
                                    foreach ($blood_groups as $key => $bg) {
                                        $selected = (isset($_POST['blood_group']) && $_POST['blood_group'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$bg}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="disease" class="form-label">Disease: (use "NA" for no disease)</label>
                                <input type="text" class="form-input" id="disease" name="disease"
                                    value="<?php echo htmlEscape($_POST['disease'] ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="notes" class="form-label">Note: (use "NA" for no notes)</label>
                                <input type="text" class="form-input" id="notes" name="notes"
                                    value="<?php echo htmlEscape($_POST['notes'] ?? ''); ?>" required>
                            </div>
                        </div>


                        <div class="flex justify-center pt-4">
                            <button type="submit" class="btn btn-primary w-full md:w-auto">
                                Register User
                            </button>
                        </div>
                    </form>

                    <p class="text-center text-gray-600 mt-6">
                        Already have an account? <a href="/src/pages/auth/login.php"
                            class="text-blue-600 hover:underline font-medium">Login here</a>.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>Blood Bank Management System.</p>
            <p class="mt-2">Contact: +00 000 00000 |
                <a href="../../pages/privacypolicy.php" class="text-blue-400 hover:underline mt-1 inline-block">Privacy
                    Policy</a>
            </p>
        </div>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
