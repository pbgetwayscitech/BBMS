<?php

session_start();
require_once '../../config/functions.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/admin_controller.php';
require_once '../../controller/admin_brequest_controller.php';
require_once '../../config/code_bloodgroups.php';
require_once '../../config/code_genders.php';
require_once '../../config/filter_searchBrequests.php';
require_once '../../model/uniqueIDRecords.php';
require_once '../../controller/user_record_controller.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    redirect('/auth/login.php');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    session_destroy();
    redirect('index.php');
    exit;
}

if (!isset($_SESSION['blood_request_tbname'])) {
    echo "Table name not set";
    exit;
}

if (!isset($_SESSION['bank_id'])) {
    echo "Bank ID not set";
    exit;
}

$bank_id = (int) $_SESSION['bank_id'];
$bank_name = $_SESSION['bank_name'];
$bank_email = $_SESSION['bank_email'];
$requests_table = $_SESSION['blood_request_tbname'];
$bank_phone_number = $_SESSION['phone_number'];
$now = date("Y-m-d H:i:s");

global $filter_requests;
$message = '';
$message_type = 'info';


/* ------------------------ CSRF Token ------------------------ */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = new_csrf_token();
}
$csrf = $_SESSION['csrf'];

function check_csrf()
{
    if (!isset($_POST['csrf']) || !match_csrf_token($_SESSION['csrf'], $_POST['csrf'])) {
        redirect('/src/pages/error.php');
        exit;
    }
}


/* ------------------------ User search by mobile (gen_donors) ------------------------ */
if (isset($_POST['search_user'])) {
    check_csrf();
    $mobile = trim($_POST['mobile'] ?? '');
    if ($mobile !== '') {

        $user_details = find_user_detail_with_phone($mobile);
        if ($user_details == null) {
            $message = "No user found with this mobile number.";
            $message_type = "warning";
        }
    }
}

/* ------------------------ Add a new request ------------------------ */
if (isset($_POST['add_request'])) {
    check_csrf();

    $requested_for = trim($_POST['requested_for'] ?? '');
    $blood_group = trim($_POST['blood_group'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $now = date("Y-m-d H:i:s");

    if ($requested_for === '' || !in_array($blood_group, $blood_groups, true)) {
        $message = "Please provide a valid patient name and blood group.";
        $message_type = "error";
    } else {

        $request_d = new BankIdBloodRequest(
            null,
            $bank_name,
            $bank_id,
            $blood_group,
            $requested_for,
            $bank_id,
            $now,
            'requested',
            $note
        );

        if (add_blood_request($requests_table, $request_d)) {
            $message = "Request added successfully.";
            $message_type = "success";
        } else {
            $message = "Request successfully.";
            $message_type = "error";
        }
    }
}

/* ------------------------ Update request status ------------------------ */
if (isset($_POST['update_request_status'])) {
    check_csrf();

    $request_id = (int) ($_POST['request_id'] ?? 0);
    $new_status = strtolower(trim($_POST['new_status'] ?? ''));

    $donor_id = intval(trim($_POST['donor_id']));
    $blood_group = trim($_POST['blood_group']);


    if ($request_id > 0 && in_array($new_status, ['fulfilled', 'discarded'], true)) {

        if (update_request_status($requests_table, $request_id, $new_status, $bank_id)) {

            // add data to user record
            $donor_detail = find_user_detail_with_donor_id($donor_id);
            $donor_phone = $donor_detail->getPhoneNumber();

            $user_record_table = '' . $donor_id . $donor_phone . '_records';
            $record_to_add = new UniqueIdRecord(
                null,
                'donation',
                $blood_group,
                "ACTION COMPLETE WITH BANK.",
                $bank_id,
                $now,
            );

            $isrecord = add_data_to_user_record($user_record_table, $record_to_add);
            if (!$isrecord) {
                $msg = "Unable to add Data to User Record.";
                $msg_type = 'danger';
            }

            $bank_blood_detail = get_blood_group_qty_for_bank_email($bank_email, $bank_id, $blood_group);
            $bank_rec = update_blood_stock_param($bank_id, $bank_phone_number, $blood_group, intval($bank_blood_detail) - 1);

            if ($bank_rec == true) {
                $msg = "Updated Sucessfully.";
            } else {
                $msg = "Unable to update data to bank stock main record.";
                $msg_type = 'danger';
            }


        } else {
            $message = "Failed to update status.";
            $message_type = "error";
        }

    } else {
        $message = "Invalid status update.";
        $message_type = "error";
    }
}

/* ------------------------ Search / Fetch Requests ------------------------ */
if (isset($_POST['search_data'])) {
    check_csrf();

    $search_filter = $_POST['search_filter'] ?? '';
    $search_query = trim($_POST['search_query'] ?? '');

    $search_data = search_from_requests($requests_table, $search_filter, $search_query);
} else {
    $search_data = search_from_requests($requests_table, '', '');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Blood Requests</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/bloodrequest.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

</head>

<body class="bg-gray-100">
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
                    <li><a href="/src/pages/auth/logout.php" class="font-bold text-orange-500">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="flex h-screen overflow-hidden text-gray-800">
        <!-- Sidebar -->
        <div class="flex flex-col w-64 sidebar text-gray-100">
            <div class="p-6">
                <h3 class="text-center text-lg font-bold text-white">Dashboard Menu</h3>
            </div>
            <nav class="flex-1 flex flex-col px-4 space-y-4">
                <a href="/src/pages/admin/dashboard.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-home text-xl text-blue-300"></i>
                        <span class="font-medium text-white">Home</span>
                    </div>
                </a>
                <a href="/src/pages/admin/bloodStock.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-exchange-alt text-xl text-blue-300"></i>
                        <span class="font-medium text-white">Blood Stock</span>
                    </div>
                </a>
                <a href="/src/pages/admin/bloodRequests.php" class="nav-card p-4 rounded-xl active">
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

        <!-- Main -->
        <main class="flex-1 overflow-x-auto overflow-y-auto p-8 bg-gray-100">

            <!-- Flash message -->
            <?php if ($message): ?>
                <div class="mb-4 p-3 rounded-md
                <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' :
                    ($message_type === 'warning' ? 'bg-yellow-100 text-yellow-800' :
                        ($message_type === 'error' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')); ?>">
                    <?php echo htmlEscape($message); ?>
                </div>
            <?php endif; ?>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- Search Card -->
                <div class="bg-white rounded-2xl card-shadow overflow-hidden col-span-1 md:col-span-2 lg:col-span-2">
                    <div class="text-xl flex m-6 items-center">
                        <i class="fas fa-search text-purple-500 mr-3"></i> Search Requests
                    </div>
                    <div>
                        <form method="post">
                            <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
                            <div class="flex items-center m-3">
                                <input type="text" placeholder="Search"
                                    class="m-3 block w-5/6 border border-gray-300 rounded-md shadow-sm p-2"
                                    id="search_query" name="search_query"
                                    value="<?php echo htmlEscape($_POST['search_query'] ?? ''); ?>">
                                <select class="mr-3 p-3 border rounded-md" id="search_filter" name="search_filter">
                                    <?php
                                    foreach ($filter_requests as $key => $filter) {
                                        $selected = (isset($_POST['search_filter']) && $_POST['search_filter'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$filter}</option>";
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="search_data" name="search_data" value="1" />
                                <button type="submit"
                                    class="w-1/6 bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition">
                                    <i class="fas fa-search text-white mr-3"></i>Search
                                </button>
                            </div>
                            <div class="mx-6 mb-4 text-sm text-gray-600">
                                <b><?php echo isset($_POST['search_query']) && trim($_POST['search_query']) !== '' ? 'Showing results for ' . htmlEscape($_POST['search_query']) : ''; ?></b>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="flex justify-end mb-4">
                    <!-- Add User Button -->
                    <button onclick="openUserModal()"
                        class="bg-purple-600 text-white mr-6 px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i> Add / Find User
                    </button>

                    <!-- Add Donation Button -->
                    <button onclick="openAddRequestModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-plus mr-2"></i> Add New Request
                    </button>
                </div>

                <!-- Requests Table -->
                <div class="bg-white rounded-2xl card-shadow overflow-hidden col-span-1 md:col-span-2 lg:col-span-3">
                    <div class="p-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-list-ul text-purple-500 mr-3"></i> Requests
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Request ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested By</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested By ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested Blood Group</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested For</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bank ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested On</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Notes</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Operation</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php if ($search_data == null): ?>
                                        <tr>
                                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                                No requests found.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($search_data as $request): ?>
                                            <tr class="bg-white hover:bg-gray-50">
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getRequestId()); ?></td>
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getRequestedBy()); ?></td>
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getRequestedById()); ?>
                                                </td>
                                                <td class="px-6 py-4 font-semibold">
                                                    <?php echo htmlEscape(strtoupper($blood_groups[$request->getRequestedBloodGroup()])); ?>
                                                </td>
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getRequestedFor()); ?>
                                                </td>
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getBankId()); ?></td>
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getRequestedOn()); ?></td>
                                                <td class="px-6 py-4">
                                                    <?php
                                                    $st = strtolower($request->getStatus());
                                                    $badgeClass = $st === 'requested' ? 'badge-warning' : ($st === 'fulfilled' ? 'badge-success' : 'badge-danger');
                                                    ?>
                                                    <span class="badge <?php echo $badgeClass; ?>">
                                                        <?php echo htmlEscape(ucfirst($st)); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4"><?php echo htmlEscape($request->getNote()); ?></td>
                                                <td class="px-6 py-4">
                                                    <?php if ($request->getStatus() == 'fulfilled' || $request->getStatus() == 'discarded') { ?>
                                                        NA
                                                    <?php } else { ?>
                                                        <form action="<?php echo htmlEscape($_SERVER['PHP_SELF']); ?>" method="post"
                                                            class="flex items-center gap-2">
                                                            <input type="hidden" name="csrf"
                                                                value="<?php echo htmlEscape($csrf); ?>">
                                                            <input type="hidden" name="update_request_status" value="1">
                                                            <input type="hidden" name="donor_id"
                                                                value="<?= $request->getRequestedById() ?>">
                                                            <input type="hidden" name="blood_group"
                                                                value="<?= $request->getRequestedBloodGroup() ?>">
                                                            <input type="hidden" name="request_id"
                                                                value="<?php echo htmlEscape($request->getRequestId()); ?>">
                                                            <select name="new_status" class="border rounded-md text-sm p-1">
                                                                <option value="fulfilled">Fulfill</option>
                                                                <option value="discarded">Discard</option>
                                                            </select>
                                                            <button type="submit"
                                                                class="bg-green-600 text-white text-sm px-3 py-1 rounded-md hover:bg-green-700 transition">
                                                                Update
                                                            </button>
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- User Modal -->
    <div id="userModal"
        class="fixed inset-0 bg-blue-500/20  <?php echo isset($_POST['search_user']) ? '' : 'hidden'; ?> z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg p-6 relative">
            <button type="button" onclick="closeUserModal()"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">✖</button>
            <h2 class="text-2xl font-bold mb-4 text-center">Find User</h2>

            <?php if (isset($_POST['search_user'])): ?>
                <?php if ($user_details != null): ?>
                    <div class="p-3 border rounded-md bg-gray-50">
                        <p><b>Donor ID:</b> <?php echo htmlEscape($user_details->getDonorId()); ?></p>
                        <p><b>Unique ID:</b> <?php echo htmlEscape($user_details->getUniqueId()); ?></p>
                        <p><b>Full Name:</b> <?php echo htmlEscape($user_details->getFullName()); ?></p>
                        <p><b>Email:</b> <?php echo htmlEscape($user_details->getEmailId()); ?></p>
                        <p><b>Father's Name:</b> <?php echo htmlEscape($user_details->getFathersName()); ?></p>
                        <p><b>Mother's Name:</b> <?php echo htmlEscape($user_details->getMothersName()); ?></p>
                        <p><b>Phone Number:</b> <?php echo htmlEscape($user_details->getPhoneNumber()); ?></p>
                        <p><b>Gender:</b> <?php echo htmlEscape($genders[$user_details->getGender()]); ?></p>
                        <p><b>Blood Group:</b> <?php echo htmlEscape($blood_groups[$user_details->getBloodGroup()]); ?></p>
                        <p><b>Disease:</b> <?php echo htmlEscape($user_details->getDisease()); ?></p>
                        <p><b>Notes:</b> <?php echo htmlEscape($user_details->getNotes()); ?></p>
                    </div>
                <?php else: ?>
                    <div class="p-3 border rounded-md bg-red-50 text-red-600">❌ No user found with this mobile number.</div>
                <?php endif; ?>

                <form method="post" class="mt-4">
                    <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md w-full">🔄 New Search</button>
                </form>
            <?php else: ?>
                <form method="post">
                    <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
                    <label for="mobile" class="block text-gray-700">Enter Mobile Number:</label>
                    <input type="text" id="mobile" name="mobile" maxlength="10"
                        class="w-full border border-gray-300 rounded-md p-2 mb-3" required>
                    <button type="submit" name="search_user"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full">
                        Search
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Request Modal -->
    <div id="addRequestModal" class="fixed inset-0 bg-blue-500/20 hidden z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg p-6 relative">
            <button onclick="closeAddRequestModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>

            <h2 class="text-2xl font-bold mb-4 text-center">Add Blood Request</h2>

            <form action="<?php echo htmlEscape($_SERVER['PHP_SELF']); ?>" method="post">
                <input type="hidden" name="csrf" value="<?php echo htmlEscape($csrf); ?>">
                <input type="hidden" name="add_request" value="1">

                <!-- Requested For -->
                <div class="mb-4">
                    <label for="requested_for" class="block text-gray-700">Requested For (Patient / Name):</label>
                    <input type="text" name="requested_for" id="requested_for"
                        class="w-full border border-gray-300 rounded-md p-2"
                        value="<?php echo htmlEscape($_POST['requested_for'] ?? ''); ?>" required>
                </div>

                <!-- Blood Group -->
                <div class="mb-4">
                    <label for="blood_group" class="block text-gray-700">Blood Group:</label>
                    <select name="blood_group" id="blood_group" class="w-full border border-gray-300 rounded-md p-2"
                        required>
                        <option value="">Select Blood Group</option>
                        <?php
                        foreach ($blood_groups as $key => $bg) {
                            $selected = (isset($_POST['blood_group']) && $_POST['blood_group'] == $key) ? 'selected' : '';
                            echo "<option value='{$key}' {$selected}>{$bg}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label for="note" class="block text-gray-700">Notes:</label>
                    <textarea name="note" id="note" rows="3"
                        class="w-full border border-gray-300 rounded-md p-2"><?php echo htmlEscape($_POST['note'] ?? ''); ?></textarea>
                </div>

                <!-- Submit -->
                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Add Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // User modal controls
        function openUserModal() {
            document.getElementById("userModal").classList.remove("hidden");
        }
        function closeUserModal() {
            document.getElementById("userModal").classList.add("hidden");
        }

        // Add request modal controls
        function openAddRequestModal() {
            document.getElementById('addRequestModal').classList.remove('hidden');
        }
        function closeAddRequestModal() {
            document.getElementById('addRequestModal').classList.add('hidden');
        }
    </script>

</body>

</html>
