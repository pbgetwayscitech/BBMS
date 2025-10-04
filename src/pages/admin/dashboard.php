<?php
session_start();

// dashboard.php
require_once '../../config/functions.php';
require_once '../../controller/admin_controller.php';
require_once '../../config/db.php';
require_once '../../config/code_states.php';

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

$bank_detail = search_admin_with_email($bank_email);
if ($bank_detail === null) {
    session_destroy();
    redirect('/src/pages/auth/login.php');
    exit;
}

if (create_other_bank_related_tables($bank_detail->getBankId(), $bank_detail->getPhoneNumber())) {
    $bstock_table_name = '' . $bank_detail->getBankId() . $bank_detail->getPhoneNumber() . '_blood_stock';
    $bdrq_table_name = '' . $bank_detail->getBankId() . $bank_detail->getPhoneNumber() . '_blood_request';
    $_SESSION['blood_stock_tbname'] = $bstock_table_name;
    $_SESSION['blood_request_tbname'] = $bdrq_table_name;
} else {
    session_destroy();
    die("unable to setup bank.");
}

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Tailwind CSS-->
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
        <div class="container mx-auto px-4 flex justify-between items-center">
            <a href="/index.php" class="flex items-center space-x-2 text-white text-2xl font-bold">
                <img src="/src/assets/logo.png" alt="Blood Bank Logo" class="w-8 h-8 rounded-full">
                <span>BBMS</span>
            </a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="/src/pages/reports.php" class="font-bold text-green-400">Reports</a></li>
                    <li><a href="/src/pages/search.php" class="main-nav-link">Search</a></li>
                    <li><a href="/src/pages/auth/registeruser.php" class="main-nav-link">Register Donor</a></li>
                    <li><a href="/src/pages/auth/logout.php" class=" font-bold text-orange-500">logout</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="flex h-screen overflow-hidden text-gray-800">

        <div class="flex flex-col w-64 sidebar text-gray-100">
            <div class="p-6">
                <h3 class="text-center text-lg font-bold text-white">Dashboard Menu</h3>
            </div>
            <nav class="flex-1 flex flex-col px-4 space-y-4">

                <a href="#" class="nav-card p-4 rounded-xl active">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-home text-xl text-blue-300"></i>
                        <span class="font-medium text-white">Home</span>
                    </div>
                </a>
                <a href="/src/pages/admin/bloodStock.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-exchange-alt text-xl text-blue-300"></i>
                        <span class="font-medium">Blood Stock</span>
                    </div>
                </a>
                <a href="/src/pages/admin/bloodRequests.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-tint text-xl text-white"></i>
                        <span class="font-medium text-white">Blood Requests</span>
                    </div>
                </a>
                <a href="/src/pages/admin/search_donor.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-search text-xl text-blue-300"></i>
                        <span class="font-medium">Donor search</span>
                    </div>
                </a>

            </nav>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex items-center justify-between h-20 px-8 header-bg">
                <div class="text-2xl font-bold text-white">
                    Welcome, <span class="text-white"><?php echo htmlEscape($bank_detail->getBankName()); ?></span>!
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 bg-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                    <div
                        class="bg-white rounded-2xl card-shadow overflow-hidden transform transition-all duration-300 hover:scale-[1.03]">
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center icon-bg text-blue-500 mr-4">
                                    <i class="fas fa-building text-2xl"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">Bank Details</h2>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-200 rounded-lg">
                                    <tbody class="text-sm text-gray-700">
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold w-40 bg-gray-50">Bank ID</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getBankId()); ?>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">Email</td>
                                            <td class="px-4 py-3">
                                                <?php echo htmlEscape($bank_detail->getBankEmail()); ?>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">Owner</td>
                                            <td class="px-4 py-3">
                                                <?php echo htmlEscape($bank_detail->getBankOwner()); ?>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">Address</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getAddress()); ?>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">Pincode</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getPincode()); ?>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-semibold bg-gray-50">State</td>
                                            <td class="px-4 py-3">
                                                <?php echo htmlEscape($states[$bank_detail->getStateId()]); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-semibold bg-gray-50">Phone</td>
                                            <td class="px-4 py-3">
                                                <?php echo htmlEscape($bank_detail->getPhoneNumber()); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white rounded-2xl card-shadow overflow-hidden transform transition-all duration-300 hover:scale-[1.03]">
                        <div class="p-8">
                            <div class="flex items-center mb-6">
                                <div
                                    class="w-12 h-12 rounded-full flex items-center justify-center icon-bg text-red-500 mr-4">
                                    <i class="fas fa-syringe text-2xl"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">Current Blood Stock</h2>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full border border-gray-200 rounded-lg">
                                    <thead>
                                        <tr class="bg-gray-100 text-gray-800 text-sm">
                                            <th class="px-4 py-3 text-left font-semibold">Blood Group</th>
                                            <th class="px-4 py-3 text-left font-semibold">Available Units</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm text-gray-700">
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">A Positive (A+)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getAStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">A Negative (A-)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getApStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">B Positive (B+)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getBStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">B Negative (B-)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getBpStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">AB Positive (AB+)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getAbStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">AB Negative (AB-)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getAbpStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="px-4 py-3 font-medium">O Positive (O+)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getOStock()); ?>
                                                units</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-3 font-medium">O Negative (O-)</td>
                                            <td class="px-4 py-3"><?php echo htmlEscape($bank_detail->getOpStock()); ?>
                                                units</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

</body>

</html>
