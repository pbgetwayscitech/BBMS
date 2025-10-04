<?php
session_start();


if (!isset($_SESSION['loggedin']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'user'], true)) {
    session_destroy();
    header("Location: /index.php");
    exit;
}

require_once '../config/functions.php';
require_once '../config/code_bloodgroups.php';
require_once '../config/code_states.php';
require_once '../controller/search_controller.php';
require_once '../model/bankIDBloodRequest.php';
require_once '../model/uniqueIDRecords.php';

$result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $search_query = trim($_POST['search_query']);
    $search_bg = trim($_POST['blood_group']);
    $search_state = trim($_POST['state']);

    $result = search_bank_with_criteria($search_query, $search_bg, $search_state);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['make_request'])) {
    $bank_id = $_POST['bank_id'];
    $bank_phone_number = $_POST['bank_phone_number'];
    $state_id = $_POST['state_id'];
    $now = date("Y-m-d H:i:s");
    $req_table_name = '' . $bank_id . $bank_phone_number . '_blood_request';
    $note = " Responce Required ";

    if ($_SESSION['role'] == 'user') {
        $requested_by = 'user';
        $requested_by_id = $_SESSION['user_id'];
    }

    if ($_SESSION['role'] == 'admin') {
        $requested_by = 'admin';
        $requested_by_id = $_SESSION['bank_id'];
    }

    $request_d = new BankIdBloodRequest(
        null,
        $requested_by,
        $requested_by_id,
        $search_bg,
        $requested_by_id,
        $bank_id,
        $now,
        'requested',
        $note
    );

    if (add_blood_request($req_table_name, $request_d)) {
        if ($_SESSION['role'] == 'user') {
            // add data to user record

            $user_record_table = '' . $requested_by_id . $_SESSION['phone_number'] . '_records';

            $user_record = new UniqueIdRecord(
                null,
                "request",
                $search_bg,
                'Requested Blood to Bank',
                $bank_id,
                ''
            );

            add_data_to_user_record($user_record_table, $user_record);

        }

        $message = 'Request sent to Blood Bank.';
        $message_type = 'success';
    } else {
        $message = 'Unable to process request.';
        $message_type = 'danger';
    }

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Blood Bank Search</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/search.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="flex flex-col">

    <!-- Header/Navigation -->
    <header class="shadow-sm py-4" style="background: #093c66ff;">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <img src="/src/assets/logo.png" alt="Blood Bank Logo" class="img-fluid" style="height: 30px; width: 30px;">
            <a href="#" class="text-2xl font-bold text-white">Blood Bank Management System</a>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="/index.php" class="text-white hover:text-blue-200 font-medium">Home</a></li>
                    <li><a href="/src/pages/auth/registeruser.php"
                            class="text-white hover:text-blue-200 font-medium">Register</a>
                    </li>
                    <li><?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                        <li><a href="/src/pages/auth/logout.php"
                                class="text-orange-500 hover:text-orange-200 font-bold">Logout</a>
                        </li>
                        <?php if ($_SESSION['role'] == "admin") { ?>
                            <li><a href="/src/pages/admin/dashboard.php"
                                    class="text-orange-500 hover:text-orange-200 font-bold">Admin
                                    Dashboard</a>
                            </li>
                        <?php } else { ?>
                            <li><a href="/src/pages/user/dashboard.php"
                                    class="text-orange-500 hover:text-orange-200 font-bold">User
                                    Dashboard</a>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li><a href="/src/pages/auth/login.php"
                                class="text-orange-500 hover:text-orange-200 font-bold">Login</a>
                        </li>
                    <?php } ?>
                    </li>
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

    <!-- The main content area now uses responsive padding to give the "full screen" feel without visible borders -->
    <div class="search-container py-6 px-4 sm:px-6 md:px-8 lg:px-10">
        <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-8 mt-6">❤️ Discover Lifesaving Blood Banks
            Near You
        </h1>

        <form id="search-form" class="space-y-6 m-10" method="post">
            <!-- Main Search Input -->
            <div>
                <label for="general-search" class="block text-sm font-medium text-gray-700 mb-2">Search by Blood Bank
                    Name, Owner, or Location</label>
                <input type="text" id="general-search" name="search_query"
                    placeholder="E.g., City Blood Bank, Delhi, etc."
                    class="block w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 shadow-sm transition duration-150 ease-in-out">
            </div>

            <!-- Filters Section -->
            <div class="filter-section p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Blood Group Filter -->
                <div>
                    <label for="blood-group-filter" class="block text-sm font-medium text-gray-700 mb-2">Blood Group
                        Needed</label>
                    <select id="blood-group-filter" name="blood_group"
                        class="block w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition duration-150 ease-in-out">
                        <?php
                        foreach ($blood_groups as $key => $bg) {
                            $selected = (isset($_POST['blood_group']) && $_POST['blood_group'] == $key) ? 'selected' : '';
                            echo "<option value='{$key}' {$selected}>{$bg}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- State Filter (assuming state_id corresponds to Indian states) -->
                <div>
                    <label for="state-filter" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <select id="state-filter" name="state"
                        class="block w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition duration-150 ease-in-out">
                        <?php
                        foreach ($states as $key => $stateName) {
                            $selected = (isset($_POST['state']) && $_POST['state'] == $key) ? 'selected' : '';
                            echo "<option value='{$key}' {$selected}>{$stateName}</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>

            <div class="text-center mt-8">
                <button type="submit" id="search-button"
                    class="search-button px-8 py-3 text-white font-semibold rounded-full shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-300">
                    Apply Filters & Search
                </button>
            </div>
        </form>

        <!-- Search Results Section -->
        <?php
        if ($result == null) {
            ?>
            <div id="search-results"
                class="bg-gray-100 p-6 rounded-lg border border-gray-200 mt-8 min-h-[250px] flex flex-col items-center justify-center text-gray-500">
                <p class="text-lg">Enter your search criteria above to find blood banks.</p>
                <!-- Results will be dynamically added here -->
            </div>
        <?php } else { ?>
            <?php foreach ($result as $record): ?>
                <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-200">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">
                            <?= htmlEscape($record->getBankName()) ?>
                        </h2>
                        <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                            ID: <?= htmlEscape($record->getBankId()) ?>
                        </span>
                    </div>

                    <!-- Owner + Contact -->
                    <div class="text-gray-600 mb-3">
                        <p><strong>Owner:</strong> <?= htmlEscape($record->getBankOwner()) ?></p>
                        <p><strong>Email:</strong> <?= htmlEscape($record->getBankEmail()) ?></p>
                        <p><strong>Phone:</strong> <?= htmlEscape($record->getPhoneNumber()) ?></p>
                    </div>

                    <!-- Address -->
                    <div class="text-gray-600 mb-4">
                        <p><strong>Address:</strong> <?= htmlEscape($record->getAddress()) ?>,
                            <span class="italic">Pincode: <?= htmlEscape($record->getPincode()) ?></span>
                        </p>
                    </div>

                    <!-- Blood Stock -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Available Blood Units</h3>
                        <div class="grid grid-cols-4 gap-3 text-center">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">A+</p>
                                <p><?= htmlEscape($record->getApStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">A-</p>
                                <p><?= htmlEscape($record->getAStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">B+</p>
                                <p><?= htmlEscape($record->getBpStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">B-</p>
                                <p><?= htmlEscape($record->getBStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">AB+</p>
                                <p><?= htmlEscape($record->getAbpStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">AB-</p>
                                <p><?= htmlEscape($record->getAbStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">O+</p>
                                <p><?= htmlEscape($record->getOpStock()) ?></p>
                            </div>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <p class="font-bold text-red-700">O-</p>
                                <p><?= htmlEscape($record->getOStock()) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Request Form -->
                    <form method="post" class="mt-4">
                        <input type="hidden" name="make_request" value="1">
                        <input type="hidden" name="search_query" value="<?= htmlEscape($_POST['search_query']) ?>">
                        <input type="hidden" name="blood_group" value="<?= htmlEscape($_POST['blood_group']) ?>">
                        <input type="hidden" name="state" value="<?= htmlEscape($record->getStateId()) ?>">
                        <input type="hidden" name="bank_id" value="<?= htmlEscape($record->getBankId()) ?>">
                        <input type="hidden" name="bank_phone_number" value="<?= htmlEscape($record->getPhoneNumber()) ?>">
                        <input type="hidden" name="state_id" value="<?= htmlEscape($record->getStateId()) ?>">
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-xl transition duration-200">
                            Make Request
                        </button>
                    </form>

                </div>

            <?php endforeach; ?>
        <?php } ?>

    </div>
</body>

</html>
