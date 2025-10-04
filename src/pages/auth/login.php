<?php

include_once "../../config/functions.php";

session_start();

if (isset($_SESSION['loggedin'])) {
    session_destroy();
    redirect('/src/pages/auth/login.php');
    exit;
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $registration = $_GET['registration'] ?? null;
    if ($registration === 'success') {
        $message = "Registration successful! You can now log in.";
        $message_type = "success";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $isadmin = $_POST['isadmin'] ?? '';

    if (!empty($email) && !empty($password) && !empty($isadmin)) {

        $hashed_password = hashPassword($password);
        if ($isadmin == "admin") {

            require_once "../../controller/admin_controller.php";

            $bank_detail = search_admin_with_email($email);
            if ($bank_detail != null && verifyPassword($password, $bank_detail->getPasswordHash())) {
                $_SESSION['loggedin'] = true;
                $_SESSION['role'] = 'admin';

                $_SESSION['bank_id'] = $bank_detail->getBankId();
                $_SESSION['bank_email'] = $bank_detail->getBankEmail();
                $_SESSION['bank_name'] = $bank_detail->getBankName();
                $_SESSION['phone_number'] = $bank_detail->getPhoneNumber();

                // Redirect to a dashboard or home page after successful login
                header("Location: /src/pages/admin/dashboard.php");
                exit;
            } else {
                // Generic error for security
                $message = "Invalid email or password.";
                $message_type = "danger";
            }

        }

        if ($isadmin == "user") {

            require_once "../../controller/user_controller.php";

            $donor_detail = search_user_with_email($email);
            if ($donor_detail != null && verifyPassword($password, $donor_detail->getPasswordHash())) {
                $_SESSION['loggedin'] = true;
                $_SESSION['role'] = 'user';
                $_SESSION['user_id'] = $donor_detail->getDonorId();
                $_SESSION['user_email'] = $donor_detail->getEmailId();
                $_SESSION['user_name'] = $donor_detail->getFullName();
                $_SESSION['phone_number'] = $donor_detail->getPhoneNumber();

                // Redirect to a dashboard or home page after successful login
                header("Location: /src/pages/user/dashboard.php");
                exit;
            } else {
                // Generic error for security
                $message = "Invalid email or password.";
                $message_type = "danger";
            }

        }

    } else {
        $message = "Please fill in all fields.";
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BBMS</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/login.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 flex flex-col min-h-screen">

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
                    <li><a href="/src/pages/auth/registerbank.php" class="main-nav-link">Register Bank</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <?php
    if (isset($message)) {
        $alert_class = ($message_type === 'success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        echo "<div class='py-3 px-4 rounded text-center {$alert_class}' role='alert'>" . htmlEscape($message) . "</div>";
    }
    ?>

    <div class="container mx-auto px-4 flex justify-center items-center my-4 flex-grow">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-sm">
            <div class="p-6">
                <form class="flex flex-col items-center px-6" method="post" action="">

                    <div class="bg-gray-100 p-1 mx-2 text-center rounded-md cursor-pointer flex justify-between gap-1">
                        <div id="user_id" onclick="select_user();"
                            class="form input flex-2 px-8 py-0.5 bg-blue-500 rounded-md text-white">
                            USER
                        </div>
                        <div id="admin_id" onclick="select_admin();"
                            class="form-input flex-2 px-8 py-0.5 bg-gray-50 rounded-md text-black">
                            ADMIN / BANK
                        </div>
                    </div>

                    <input type="hidden" name="isadmin" id="isadmin" value="user">

                    <div class="flex justify-center mt-6">
                        <img src="/src/assets/logo.png" class="h-12 w-12 rounded-full" alt="Blood Bank Logo">
                    </div>
                    <div class="text-center mt-4">
                        <h1 class="font-sans text-3xl font-bold text-blue-700">Login</h1>
                    </div>

                    <div class="mb-4 w-full mt-6">
                        <label for="email" class="block text-gray-700 text-sm font-medium mb-1">Email:</label>
                        <input type="email" name="email"
                            class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="email" placeholder="Email" required>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="password" class="block text-gray-700 text-sm font-medium mb-1">Password:</label>
                        <input type="password" name="password"
                            class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="password" placeholder="Password" required>
                    </div>
                    <div class="flex justify-center w-full mt-6 space-x-3">
                        <button type="submit"
                            class="btn bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-5 text-center w-full mt-auto">
        <div class="container mx-auto px-4">
        </div>
    </footer>

    <script>
        // JavaScript for toggle functionality
        var m_admin = document.getElementById("admin_id");
        var m_user = document.getElementById("user_id");
        var isadmin_input = document.getElementById("isadmin");

        function select_admin() {
            m_user.classList.remove("bg-blue-500", "text-white");
            m_user.classList.add("bg-gray-50", "text-black");

            m_admin.classList.remove("bg-gray-50", "text-black");
            m_admin.classList.add("bg-blue-500", "text-white");

            isadmin_input.value = "admin";
        }

        function select_user() {
            m_admin.classList.remove("bg-blue-500", "text-white");
            m_admin.classList.add("bg-gray-50", "text-black");

            m_user.classList.remove("bg-gray-50", "text-black");
            m_user.classList.add("bg-blue-500", "text-white");

            isadmin_input.value = "user";
        }
    </script>
</body>

</html>
