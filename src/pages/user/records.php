<?php
// records.php

session_start();
require_once '../../config/functions.php';
require_once '../../config/filter_records.php';
require_once '../../config/code_bloodgroups.php';
require_once '../../controller/user_record_controller.php';

$records = [];

/* ---------------- Auth & Security ---------------- */
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    redirect('/src/pages/auth/login.php');
    exit;
}

if ($_SESSION['role'] !== 'user') {
    session_destroy();
    redirect('/index.php');
    exit;
}

if (!isset($_SESSION['donor_records_tbname'])) {
    session_destroy();
    redirect('/index.php');
    exit;
}
$records_table_name = $_SESSION['donor_records_tbname'];

/* ---------------- Handle Search ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_query = trim($_POST['search_query'] ?? '');
    $filter_search = $_POST['filter_search'] ?? 'all';

    $records = find_records($records_table_name, $search_query, $filter_search);
} else {
    $records = find_records($records_table_name, '', 'all');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Donor ID Records</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <li><a href="/src/pages/search.php" class="text-white font-bold">Search Blood</a></li>
                    <li><a href="/src/pages/auth/logout.php"
                            class="text-orange-400 font-bold hover:text-orange-300">Logout</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-gray-100 p-6">
            <h3 class="text-lg font-bold mb-6">Menu</h3>
            <nav class="space-y-4">
                <a href="/src/pages/user/dashboard.php"
                    class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-user text-blue-300"></i><span>Profile</span>
                </a>
                <a href="/src/pages/user/records.php"
                    class="flex items-center space-x-3 p-3 rounded-lg bg-gray-700 hover:bg-gray-600">
                    <i class="fas fa-history text-green-300"></i><span>Records</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">

            <!-- Page Title -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl p-6 mb-8 shadow-lg text-white">
                <h2 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-search mr-3"></i> Search Donor Records
                </h2>
                <p class="text-sm opacity-80">Filter your donation and request history</p>
            </div>

            <!-- Search Form -->
            <div class="bg-white rounded-2xl shadow p-6 mb-8">
                <form method="post" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="col-span-2">
                        <label class="block mb-1 text-sm font-medium text-green-500">Search Records</label>
                        <input type="text" name="search_query" class="border rounded-lg p-2 w-full"
                            placeholder="Search Records">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium">Search Filter</label>
                        <select name="filter_search" class="border rounded-lg p-2 w-full">
                            <option value="all">All</option>
                            <?php
                            foreach ($filter_records as $key => $filter) {
                                $selected = (isset($_POST['filter_search']) && $_POST['filter_search'] == $key) ? 'selected' : '';
                                echo "<option value='{$key}' {$selected}>{$filter}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-span-full flex justify-end">
                        <input type="hidden" name="search" value="1">
                        <button type="submit" name="search"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow hover:bg-indigo-700 transition">
                            <i class="fas fa-search mr-2"></i> Search
                        </button>
                    </div>
                    <div class="col-span-full text-sm text-black mt-2">
                        <?php echo (isset($_POST['search_query']) && trim($_POST['search_query']) != '')
                            ? 'showing result for "' . $_POST['search_query'] . '" : '
                            : '';
                        ?>
                    </div>
                </form>
            </div>

            <!-- Records Table -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center text-gray-700">
                    <i class="fas fa-list-alt text-indigo-500 mr-2"></i> Record History
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border rounded-lg overflow-hidden">
                        <thead class="bg-gray-200 text-gray-700">
                            <tr>
                                <th class="p-3 border">Record Number</th>
                                <th class="p-3 border">Type</th>
                                <th class="p-3 border">Blood Group</th>
                                <th class="p-3 border">Note</th>
                                <th class="p-3 border">Bank ID</th>
                                <th class="p-3 border">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $rec): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 border text-center"><?= $rec->getRecordNumber() ?></td>
                                    <td class="p-3 border text-center font-medium capitalize"><?= $rec->getRecordType() ?>
                                    </td>
                                    <td class="p-3 border text-center font-bold text-red-500">
                                        <?= $blood_groups[$rec->getBloodGroup()] ?>
                                    </td>
                                    <td class="p-3 border text-center"><?= htmlEscape($rec->getNote()) ?></td>
                                    <td class="p-3 border text-center"><?= htmlEscape($rec->getBankId()) ?></td>
                                    <td class="p-3 border text-sm text-center text-gray-600"><?= $rec->getDate() ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($records)): ?>
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-gray-500">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>
</body>

</html>
