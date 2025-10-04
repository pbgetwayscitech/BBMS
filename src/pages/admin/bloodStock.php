<?php
session_start();

require_once '../../config/functions.php';
require_once '../../config/code_states.php';
require_once '../../config/code_genders.php';
require_once '../../config/code_bloodgroups.php';
require_once '../../config/filter_searchBstock.php';
require_once '../../controller/user_controller.php';
require_once '../../controller/admin_bstock_controller.php';
require_once '../../model/bankIDbloodStock.php';
require_once '../../controller/user_record_controller.php';
require_once '../../controller/admin_controller.php';
require_once '../../model/uniqueIDRecords.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    redirect('/auth/login.php');
    return false;
}

if ($_SESSION['role'] != 'admin') {
    session_destroy();
    redirect('/index.php');
    exit;
}

if (!isset($_SESSION['blood_stock_tbname'])) {
    echo "table name not set";
    exit;
}

$isloggedin = $_SESSION['loggedin'];
$role = $_SESSION['role'];
$blood_stock_tablename = $_SESSION['blood_stock_tbname'] ?? '';
$bank_id = $_SESSION['bank_id'];
$bank_phone_number = $_SESSION['phone_number'];
$bank_email = $_SESSION['bank_email'];
$now = date("Y-m-d H:i:s");
$msg = '';
$msg_type = '';

// Search user by mobile
if (isset($_POST['search_user'])) {
    $mobile = trim($_POST['mobile']);
    $user_details = find_user_detail_with_phone($mobile);
    if ($user_details == null) {
        // Not found → ask for registration
        $showRegisterForm = true;
    }
}

// --- add New Donation ---
if (isset($_POST['add_donation'])) {

    $donor_id = intval(trim($_POST['donor_id']));
    $blood_group = trim($_POST['blood_group']);
    $donation_date = date("Y-m-d");
    $expiry_date = trim(date("Y-m-d", strtotime("+42 days")));
    $note = trim($_POST['note']);

    if ($note == '') {
        $msg = "Please add notes.";
        $msg_type = "error";
    } else {

        $donation_data = new BankIdBloodStock(
            null,
            $donor_id,
            $bank_id,
            $donation_date,
            $blood_group,
            $expiry_date,
            $note,
            'preserved',
            $now
        );

        $register_status = add_new_donation($blood_stock_tablename, $donation_data);
        if (!$register_status) {
            $msg = "Error adding donation: Donor Not Found. ";
            $msg_type = "error";
        } else {

            // add data to user record
            $user_record_table = '' . $donor_id . $_POST['phone_number'] . '_records';
            $record_to_add = new UniqueIdRecord(
                null,
                'donation',
                $blood_group,
                "DONATION RECORDED WITH BANK",
                $bank_id,
                $now,
            );

            $isrecord = add_data_to_user_record($user_record_table, $record_to_add);
            if (!$isrecord) {
                $msg = "Unable to add data to user record.";
                $msg_type = "danger";
            }

            // add data to main blood_bank Table
            $bank_blood_detail = get_blood_group_qty_for_bank_email($bank_email, $bank_id, $blood_group);
            $bank_rec = update_blood_stock_param($bank_id, $bank_phone_number, $blood_group, intval($bank_blood_detail) + 1);

            if ($bank_rec == true) {
                $msg = "Donation Added Sucessfully.";
            } else {
                $msg = "Unable to donated blood stock to blood bank main record.";
                $msg_type = "danger";
            }

        }
    }

}

// Update stock status
if (isset($_POST['update_stock_status'])) {

    $stock_id = intval(trim($_POST['stock_id']));
    $new_status = $_POST['new_status'];
    $donor_id = intval(trim($_POST['donor_id']));
    $blood_group = trim($_POST['blood_group']);

    $res = update_blood_Stock_status($blood_stock_tablename, $stock_id, $new_status);
    if (!$res) {
        $msg = "Update was not Sucessful";
        $msg_type = "danger";
        // add this data to user record
    } else {

        // add data to user record
        $donor_detail = find_user_detail_with_donor_id($donor_id);
        $donor_phone = $donor_detail->getPhoneNumber();

        $user_record_table = '' . $donor_id . $donor_phone . '_records';
        $record_to_add = new UniqueIdRecord(
            null,
            'donation',
            $blood_group,
            "DONATION RECORDED WITH BANK",
            $bank_id,
            $now,
        );

        $isrecord = add_data_to_user_record($user_record_table, $record_to_add);
        if (!$isrecord) {
            $msg = "Unable to add Data to User Record.";
            $msg_type = 'danger';
        }

        // add data to main blood_bank Table
        $bank_blood_detail = get_blood_group_qty_for_bank_email($bank_email, $bank_id, $blood_group);
        $bank_rec = update_blood_stock_param($bank_id, $bank_phone_number, $blood_group, intval($bank_blood_detail) - 1);

        if ($bank_rec == true) {
            $msg = "Updated Sucessfully.";
        } else {
            $msg = "Unable to update data to bank stock main record.";
            $msg_type = 'danger';
        }

    }
}

if (isset($_POST['search_data'])) {
    $search_query = $_POST['search_query'];
    $search_filter = $_POST['search_filter'];

    $stock_result = search_blood_stock($blood_stock_tablename, $search_query, $search_filter);
} else {
    $stock_result = search_blood_stock($blood_stock_tablename, ' ', ' ');
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blood Stock</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <!-- Page Specific CSS -->
    <link href="/src/css/bloodstock.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
                    <li><a href="/src/pages/auth/registeruser.php" class="main-nav-link ">Register Donor</a>
                    </li>
                    <li><a href="/src/pages/auth/logout.php" class="font-bold text-orange-500">logout</a></li>
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

                <a href="/src/pages//admin/dashboard.php" class="nav-card p-4 rounded-xl">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-home text-xl text-blue-300"></i>
                        <span class="font-medium text-white">Home</span>
                    </div>
                </a>
                <a href="/src/pages/admin/bloodStock.php" class="nav-card p-4 rounded-xl active">
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
                        Search Transaction
                    </div>
                    <div>
                        <form method="post">
                            <div class="flex items-center m-3">

                                <input type="text" placeholder="Search"
                                    class="form-input m-3 block w-5/6 border border-gray-300 rounded-md shadow-sm p-2"
                                    id="search_query" name="search_query"
                                    value="<?php echo htmlEscape($_POST['search_query'] ?? ''); ?>" required>
                                <select class="mr-3 from-select p-3" id="search_filter" name="search_filter" required>
                                    <?php
                                    foreach ($filter_searchBstock as $key => $filter) {
                                        $selected = (isset($_POST['search_filter']) && $_POST['search_filter'] == $key) ? 'selected' : '';
                                        echo "<option value='{$key}' {$selected}>{$filter}</option>";
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="search_data" name="search_data" value="1" />
                                <button type="submit"
                                    class="w-1/6 bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 transition duration-300">
                                    <i class="fas fa-search text-white mr-3"></i>Search</button>

                            </div>
                        </form>
                    </div>
                </div>


                <div class="flex justify-end mb-4">
                    <!-- Add User Button -->
                    <button onclick="openUserModal()"
                        class="bg-purple-600 text-white mr-6 px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition duration-300">
                        <i class="fas fa-user-plus mr-2"></i> ADD DONATION
                    </button>
                </div>

                <!-- User Modal -->
                <div id="userModal"
                    class="fixed inset-0 bg-green-500/20 <?= (isset($_POST['search_user']) || isset($_POST['register_user'])) ? '' : 'hidden' ?> z-50 flex items-center justify-center">
                    <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg p-6 relative">

                        <!-- Close -->
                        <button type="button" onclick="document.getElementById('userModal').classList.add('hidden')"
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-xl">
                            ✖
                        </button>

                        <h2 class="text-2xl font-bold mb-4 text-center">Find User</h2>

                        <?php if (isset($_POST['search_user'])): ?>
                            <?php if ($user_details != null): ?>
                                <!-- User Found -->
                                <div class="p-3 border rounded-md bg-gray-50">
                                    <p><b>Donor ID:</b> <?= htmlEscape($user_details->getDonorId()) ?></p>
                                    <p><b>Unique ID:</b> <?= htmlEscape($user_details->getUniqueId()) ?></p>
                                    <p><b>Full Name:</b> <?= htmlEscape($user_details->getFullName()) ?></p>
                                    <p><b>Email:</b> <?= htmlEscape($user_details->getEmailId()) ?></p>
                                    <p><b>Father's Name:</b> <?= htmlEscape($user_details->getFathersName()) ?></p>
                                    <p><b>Mother's Name:</b> <?= htmlEscape($user_details->getMothersName()) ?></p>
                                    <p><b>Phone Number:</b> <?= htmlEscape($user_details->getPhoneNumber()) ?></p>
                                    <p><b>Gender:</b> <?= htmlEscape($genders[$user_details->getGender()]) ?></p>
                                    <p><b>Blood Group:</b> <?= htmlEscape($blood_groups[$user_details->getBloodGroup()]) ?></p>
                                    <p><b>Disease:</b> <?= htmlEscape($user_details->getDisease()) ?></p>
                                    <p><b>Notes:</b> <?= htmlEscape($user_details->getNotes()) ?></p>
                                </div>

                                <!-- Make Donation -->
                                <form method="post" class="mt-4">

                                    <input type="hidden" name="add_donation" value="1" />
                                    <input type="hidden" name="donor_id"
                                        value="<?= htmlEscape($user_details->getDonorId()) ?>" />
                                    <input type="hidden" name="blood_group"
                                        value="<?= htmlEscape($user_details->getBloodGroup()) ?>" />
                                    <input type="hidden" name="phone_number"
                                        value="<?= htmlEscape($user_details->getPhoneNumber()) ?>" />

                                    <!-- Notes -->
                                    <div class="mb-4">
                                        <label for="note" class="block text-gray-700">Notes:</label>
                                        <textarea name="note" id="note" rows="3"
                                            class="w-full border border-gray-300 rounded-md p-2"><?php echo htmlEscape($_POST['note'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="">
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md w-full">
                                            ADD Donation
                                        </button>
                                    </div>
                                </form>

                            <?php else: ?>
                                <!-- User Not Found -->
                                <div class="p-3 border rounded-md bg-red-50 text-red-600 animate-pulse">
                                    ❌ No user found with this mobile number.
                                </div>

                            <?php endif; ?>
                            <!-- Back to Search -->
                            <form method="post" class="mt-4">
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md w-full">
                                    New Search
                                </button>
                            </form>

                        <?php else: ?>
                            <!-- Search Form -->
                            <form method="post">
                                <label for="mobile" class="block text-gray-700">Enter Mobile Number:</label>
                                <input type="text" id="mobile" name="mobile" maxlength="10"
                                    class="w-full border border-gray-300 rounded-md p-2 mb-3" required>
                                <button type="submit" name="search_user"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 w-full">
                                    Search
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="bg-white rounded-2xl card-shadow overflow-hidden col-span-1 md:col-span-2 lg:col-span-3">
                    <div class="p-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center"><i
                                class="fas fa-list-ul text-purple-500 mr-3"></i> Collections</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Donor ID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Blood Group</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Notes</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Expiary Date</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock Status</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status Date</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            OPRATION</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (!empty($stock_result)): ?>
                                        <?php foreach ($stock_result as $stock): ?>
                                            <tr>
                                                <td class="px-6 py-4"><?= htmlEscape($stock->getStockId()) ?></td>
                                                <td class="px-6 py-4"><?= htmlEscape($stock->getDonorId()) ?></td>
                                                <td class="px-6 py-4">
                                                    <?= strtoupper(htmlEscape($blood_groups[$stock->getBloodGroup()])) ?>
                                                </td>
                                                <td class="px-6 py-4">1 unit</td>
                                                <td class="px-6 py-4"><?= htmlEscape($stock->getNote()) ?></td>
                                                <td class="px-6 py-4"><?= htmlEscape($stock->getDonationDate()) ?></td>
                                                <td class="px-6 py-4"><?= htmlEscape($stock->getExpiaryDate()) ?></td>
                                                <td class="px-6 py-4">
                                                    <?= htmlEscape($stock->getStockStatus()) ?>
                                                </td>
                                                <td class="px-6 py-4"><?= htmlEscape($stock->getStockStatusDate()) ?></td>
                                                <td class="px-6 py-4">
                                                    <?php if ($stock->getStockStatus() == 'utilised' || $stock->getStockStatus() == 'discarded') { ?>
                                                        NA
                                                    <?php } else { ?>
                                                        <form method="post" class="flex items-center space-x-2">
                                                            <input type="hidden" name="stock_id"
                                                                value="<?= $stock->getStockId() ?>">
                                                            <input type="hidden" name="donor_id"
                                                                value="<?= $stock->getDonorId() ?>">
                                                            <input type="hidden" name="blood_group"
                                                                value="<?= $stock->getBloodGroup() ?>">
                                                            <select name="new_status" class="border rounded p-1">
                                                                <option value="">Select</option>
                                                                <option value="utilised">Utilised</option>
                                                                <option value="discarded">Discarded</option>
                                                            </select>
                                                            <button type="submit" name="update_stock_status"
                                                                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                                                                Update
                                                            </button>
                                                        </form>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                                No stock records found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <script>
            function openUserModal() {
                document.getElementById("userModal").classList.remove("hidden");
                backToSearch();
            }
            function closeUserModal() {
                document.getElementById("userModal").classList.add("hidden");
            }

            function backToSearch() {
                document.getElementById("stepSearch").classList.remove("hidden");
                document.getElementById("stepDetails").classList.add("hidden");
                document.getElementById("stepRegister").classList.add("hidden");
                document.getElementById("searchMsg").textContent = "";
                document.getElementById("searchMobile").value = "";
            }

        </script>

</body>

</html>
