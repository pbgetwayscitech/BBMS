<?php
session_start();
require_once '../../config/functions.php';
require_once '../../controller/user_controller.php';

require_once '../../config/code_states.php';
require_once '../../config/code_bloodgroups.php';
require_once '../../config/code_genders.php';

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

// stored at login
$donor_id = $_SESSION['user_id'];
$donor_email = $_SESSION['user_email'];
$donor_name = $_SESSION['user_name'];

$donor_detail = search_user_with_email($donor_email);
if ($donor_detail != null) {
    $tb = create_donor_relatedtables($donor_detail->getDonorId(), $donor_detail->getPhoneNumber());
    $table_name = '' . $donor_detail->getDonorId() . $donor_detail->getPhoneNumber() . '_records';
    $_SESSION['donor_records_tbname'] = $table_name;
} else {
    session_destroy();
    redirect('/index.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>

<body class="bg-gray-100 font-sans">

    <!-- Header/Navigation -->
    <header class="shadow-sm py-4" style="background: #093c66ff;">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <img src="/src/assets/logo.png" alt="Blood Bank Logo" class="img-fluid" style="height: 30px; width: 30px;">
            <a href="#" class="text-2xl font-bold text-white">Blood Bank Management System</a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="/index.php" class="text-white font-bold">Home</a></li>
                    <li><a href="/src/pages/auth/updateuser.php" class="text-white font-bold">Update Profile</a></li>
                    <li><a href="/src/pages/search.php" class="text-white font-bold">Search Blood</a></li>
                    <li><a href="/src/pages/auth/logout.php"
                            class="text-orange-400 font-bold hover:text-orange-300">Logout</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-gray-100 p-6">
            <h3 class="text-lg font-bold mb-6">Menu</h3>
            <nav class="space-y-4">
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg bg-gray-700 hover:bg-gray-600">
                    <i class="fas fa-user text-blue-300"></i><span>Dashboard</span>
                </a>
                <a href="/src/pages/user/records.php"
                    class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-history text-green-300"></i><span>Records</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">

            <!-- Welcome -->
            <div class="bg-gradient-to-r from-pink-500 to-orange-500 rounded-2xl p-6 mb-8 shadow-lg text-white">
                <h2 class="text-2xl font-bold">Welcome, <?php echo htmlEscape($donor_detail->getFullName()); ?>!</h2>
                <p class="text-sm opacity-80">Unique ID: <?php echo htmlEscape($donor_detail->getUniqueId()); ?></p>
            </div>

            <!-- Profile & Blood Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Profile Info -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-id-card mr-2 text-indigo-500"></i> Profile Information
                    </h3>
                    <table class="w-full text-sm">
                        <tr class="border-b">
                            <td class="py-2 font-medium">Email</td>
                            <td><?php echo htmlEscape($donor_detail->getEmailId()); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium">Father</td>
                            <td><?php echo htmlEscape($donor_detail->getFathersName()); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium">Mother</td>
                            <td><?php echo htmlEscape($donor_detail->getMothersName()); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium">Gender</td>
                            <td><?php echo htmlEscape($genders[$donor_detail->getGender()]); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium">Date of Birth</td>
                            <td><?php echo htmlEscape($donor_detail->getDOB()); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium">Phone</td>
                            <td><?php echo htmlEscape($donor_detail->getPhoneNumber()); ?></td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2 font-medium">Address</td>
                            <td><?php echo htmlEscape($donor_detail->getAddress()); ?>,
                                <?php echo htmlEscape($donor_detail->getPincode()); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2 font-medium">State</td>
                            <td><?php echo htmlEscape($states[$donor_detail->getStateCode()]); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Blood Info -->
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-tint mr-2 text-red-500"></i> Blood Information
                    </h3>
                    <div class="text-center py-6">
                        <div
                            class="inline-block px-6 py-4 bg-red-500 text-white rounded-full text-2xl font-bold shadow">
                            <?php echo strtoupper(htmlEscape($blood_groups[$donor_detail->getBloodGroup()])); ?>
                        </div>
                        <p class="mt-4 text-gray-600">Blood Group</p>
                    </div>
                    <div class="mt-6">
                        <p class="text-sm"><b>Diseases:</b> <?php echo htmlEscape($donor_detail->getDisease()); ?></p>
                        <p class="text-sm mt-2"><b>Notes:</b> <?php echo htmlEscape($donor_detail->getNotes()); ?></p>
                    </div>
                </div>

            </div>

        </main>
    </div>
</body>

</html>
