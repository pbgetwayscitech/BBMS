<?php

session_start();

require_once '../../config/functions.php';
require_once '../../config/code_states.php';
require_once '../../config/code_bloodgroups.php';
require_once '../../controller/user_controller.php';
require_once '../../model/genDonor.php';

// check authentication
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    redirect('/src/pages/auth/login.php');
    exit;
}
$isloggedin = $_SESSION['loggedin'];
$role = $_SESSION['role'];
if ($_SESSION['role'] != 'admin') {
    session_destroy();
    redirect('/index.php');
    exit;
}
$bank_id = $_SESSION['bank_id'];
$bank_email = $_SESSION['bank_email'];
$bank_name = $_SESSION['bank_name'];

if (isset($_POST['find_donors'])) {

    $donors = find_user_with_bg_in_state($_POST['blood_group'], $_POST['state_code']);

    if (!$donors) {
        $msg = "No donors found with the selected criteria.";
        $msg_type = 'error';
    }

}


?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Search</title> <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/admindash.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    </style>
</head>

<body class="font-sans leading-normal tracking-normal">
    <header class="main-header">
        <div class="container mx-auto px-4 flex justify-between items-center"> <a href="/index.php"
                class="flex items-center space-x-2 text-white text-2xl font-bold"> <img src="/src/assets/logo.png"
                    alt="Blood Bank Logo" class="w-8 h-8 rounded-full"> <span>BBMS</span> </a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="/src/pages/search.php" class="main-nav-link">Search</a></li>
                    <li><a href="/src/pages/auth/registeruser.php" class="main-nav-link">Register Donor</a></li>
                    <li><a href="/src/pages/auth/logout.php" class=" font-bold text-orange-500">logout</a> </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="flex h-screen overflow-hidden text-gray-800">
        <div class="flex flex-col w-64 sidebar text-gray-100">
            <div class="p-6">
                <h3 class="text-center text-lg font-bold text-white">Dashboard Menu</h3>
            </div>
            <nav class="flex-1 flex flex-col px-4 space-y-4"> <a href="#" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4"> <i class="fas fa-home text-xl text-blue-300"></i> <span
                            class="font-medium text-white">Home</span> </div>
                </a> <a href="/src/pages/admin/bloodStock.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4"> <i class="fas fa-exchange-alt text-xl text-blue-300"></i>
                        <span class="font-medium">Blood Stock</span>
                    </div>
                </a> <a href="/src/pages/admin/bloodRequests.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4"> <i class="fas fa-tint text-xl text-white"></i> <span
                            class="font-medium text-white">Blood Requests</span> </div>
                </a> <a href="/src/pages/admin/search_donor.php" class="nav-card p-4 rounded-xl active">
                    <div class="flex items-center space-x-4"> <i class="fas fa-search text-xl text-blue-300"></i> <span
                            class="font-medium">Donor search</span> </div>
                </a> </nav>
        </div>


        <main class="flex-1 overflow-x-auto overflow-y-auto p-8 bg-gray-100">
            <div class="">
                <?php if (!empty($msg)): ?>
                    <?php if ($msg_type == '') { ?>
                        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                            <?= htmlEscape($msg) ?>
                        </div>
                    <?php } else { ?>
                        <div class="bg-orange-100 text-red-800 px-4 py-2 rounded mb-4">
                            <?= htmlEscape($msg) ?>
                        </div>
                    <?php } ?>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl card-shadow overflow-hidden col-span-1 md:col-span-2 lg:col-span-2">
                    <div class="text-xl flex m-6 items-center"><i class="fas fa-search text-purple-500 mr-3"></i>
                        Search Donor
                    </div>
                    <div>
                        <form method="post">
                            <div class="flex items-center m-3">

                                <label class="mr-3 p-3" for="blood_group">Find Donors with Blood Group </label>
                                <select class="mr-3 from-select p-3" id="blood_group" name="blood_group" required>
                                    <?php
                                    foreach ($blood_groups as $key => $filter) {
                                        $selected = (isset($_POST['blood_group']) && $_POST['blood_group'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$filter}</option>";
                                    }
                                    ?>
                                </select>

                                <label class="mr-3 p-3" for="blood_group"> from State </label>
                                <select class="mr-3 from-select p-3" id="state_code" name="state_code" required>
                                    <?php
                                    foreach ($states as $key => $filter) {
                                        $selected = (isset($_POST['state_code']) && $_POST['state_code'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$filter}</option>";
                                    }
                                    ?>
                                </select>

                                <input type="hidden" id="find_donors" name="find_donors" value="1" />
                                <button type="submit"
                                    class="w-1/6 bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">
                                    <i class="fas fa-search text-white mr-3"></i>Search</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="bg-white rounded-2xl card-shadow overflow-hidden col-span-1 md:col-span-2 lg:col-span-3">
                <div class="p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center"><i
                            class="fas fa-list-ul text-purple-500 mr-3"></i> Donors </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Donor ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Full Name</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Blood Group</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        DOB</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gender</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Phone Number</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Disease</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NOTE</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (!empty($donors)): ?>
                                    <?php foreach ($donors as $donor): ?>
                                        <tr>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getDonorId()) ?></td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getFullName()) ?></td>
                                            <td class="px-6 py-4">
                                                <?= strtoupper(htmlEscape($blood_groups[$donor->getBloodGroup()])) ?>
                                            </td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getDob()) ?></td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getGender()) ?></td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getEmailId()) ?></td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getPhoneNumber()) ?></td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getDisease()) ?></td>
                                            <td class="px-6 py-4"><?= htmlEscape($donor->getNotes()) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                            No records found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>

</html>
