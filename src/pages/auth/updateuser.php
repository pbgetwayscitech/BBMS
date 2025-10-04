<?php
session_start();

require_once '../../config/functions.php';
require_once '../../controller/user_controller.php';
require_once '../../config/code_bloodgroups.php';
require_once '../../config/code_genders.php';
require_once '../../config/code_states.php';

// Ensure logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    session_destroy();
    redirect('/src/pages/auth/login.php');
    exit;
}

if ($_SESSION['role'] !== 'user') {
    session_destroy();
    redirect('/index.php');
    exit;
}

if (!isset($_SESSION['csrf']) || $_SESSION['csrf'] == '') {
    $_SESSION['csrf'] = new_csrf_token();
}

// stored at login
$donor_id = $_SESSION['user_id'];
$donor_email = $_SESSION['user_email'];
$donor_name = $_SESSION['user_name'];
$donor_detail = search_user_with_email($donor_email);

if ($donor_detail == null) {
    session_destroy();
    redirect('/index.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf = trim($_POST['csrf']);
    if (!match_csrf_token($_SESSION['csrf'], $csrf)) {
        session_destroy();
        redirect("/src/pages/error.php");
    }

    // Sanitize and validate inputs
    $full_name = trim($_POST['fullname']);
    $fathers_name = trim($_POST['fathers_name']);
    $mothers_name = trim($_POST['mothers_name']);
    $dob = trim($_POST['dob']);
    $address = trim($_POST['address']);
    $state_code = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $disease = trim($_POST['disease']);
    $notes = trim($_POST['notes']);

    // Basic validation for profile update
    if (
        empty($full_name) || empty($fathers_name) || empty($mothers_name) ||
        empty($address) || empty($state_code) || empty($pincode) ||
        empty($gender) || empty($blood_group) || empty($disease) || empty($dob)
    ) {
        $message = 'Please fill in all required fields.';
        $message_type = 'danger';
    } else {

        $infUser = new GenDonor(
            $donor_id,
            $full_name,
            ' ',
            $fathers_name,
            $mothers_name,
            $dob,
            $address,
            0,
            $state_code,
            $pincode,
            $gender,
            $blood_group,
            $disease,
            $notes,
            0,
            ' '
        );

        $update_req = update_user($infUser);
        if (!$update_req) {
            $message = 'Error Updating Details.';
            $message_type = 'danger';
        } else {
            session_destroy();
            header("Location: /src/pages/auth/login.php?registration=success");
            exit;
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
                    <li><a href="/index.php" class="main-nav-link">Home</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="flex justify-center items-center py-8">
            <div class="w-full max-w-2xl">
                <div class="card">
                    <h2 class="card-title text-center mb-6">Update Profile</h2>

                    <?php
                    if (!empty($message)) {
                        $alert_class = ($message_type === 'success') ? 'alert-success' : 'alert-danger';
                        echo "<div class='alert {$alert_class}' role='alert'>" . htmlEscape($message) . "</div>";
                    }
                    ?>

                    <form action="<?php echo htmlEscape($_SERVER['PHP_SELF']); ?>" method="post" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="fullname" class="form-label">Full Name:</label>
                                <input type="text" class="form-input" id="fullname" name="fullname"
                                    value="<?php echo htmlEscape($donor_detail->getFullName() ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="dob" class="form-label">Date of Birth:</label>
                                <input type="date" class="form-input w-full" id="dob" name="dob"
                                    value="<?php echo htmlEscape($donor_detail->getDOB() ?? ''); ?>" required>
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="fathers_name" class="form-label">Father's Name:</label>
                                <input type="text" class="form-input" id="fathers_name" name="fathers_name"
                                    value="<?php echo htmlEscape($donor_detail->getFathersName() ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="mothers_name" class="form-label">Mother's Name:</label>
                                <input type="text" class="form-input" id="mothers_name" name="mothers_name"
                                    value="<?php echo htmlEscape($donor_detail->getMothersName() ?? ''); ?>" required>
                            </div>
                        </div>

                        <div>
                            <label for="address" class="form-label">Address:</label>
                            <textarea class="form-textarea" id="address" name="address" rows="3"
                                required><?php echo htmlEscape($donor_detail->getAddress() ?? ''); ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="state" class="form-label">State:</label>
                                <select class="form-select form-input" name="state" id="state" required
                                    pattern="[0-9]{1,2}">
                                    <?php
                                    foreach ($states as $key => $stateName) {
                                        $selected = ($donor_detail->getStateCode() == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$stateName}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="pincode" class="form-label">PIN CODE:</label>
                                <input type="tel" class="form-input" name="pincode" id="pincode"
                                    value="<?php echo htmlEscape($donor_detail->getPincode() ?? ''); ?>" required
                                    pattern="[0-9]{6}">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="gender" class="form-label">Gender:</label>
                                <select class="form-select form-input" name="gender" id="gender" required
                                    pattern="[0-9]{1}">
                                    <?php
                                    foreach ($genders as $key => $gender) {
                                        $selected = ($donor_detail->getGender() == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$gender}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="blood_group" class="form-label">Blood Group:</label>
                                <select class="form-select form-input" name="blood_group" id="blood_group" required
                                    pattern="[0-9]{1}">
                                    <?php
                                    foreach ($blood_groups as $key => $bg) {
                                        $selected = ($donor_detail->getBloodGroup() == $key) ? 'selected' : '';
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
                                    value="<?php echo htmlEscape($donor_detail->getDisease() ?? ''); ?>" required>
                            </div>
                            <div>
                                <label for="notes" class="form-label">Note: (use "NA" for no notes)</label>
                                <input type="text" class="form-input" id="notes" name="notes"
                                    value="<?php echo htmlEscape($donor_detail->getNotes() ?? ''); ?>" required>
                            </div>
                        </div>

                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf'] ?>">
                        <div class="flex justify-center pt-4">
                            <button type="submit" class="btn btn-primary w-full md:w-auto">
                                Update Profile
                            </button>
                        </div>
                    </form>
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
