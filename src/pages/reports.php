<?php
// reports.php
session_start();


if (!isset($_SESSION['loggedin']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: /auth/login.php");
    exit;
}

require_once '../config/functions.php';
require_once '../config/db.php';
require_once '../controller/reports_controller.php';
require_once '../config/code_bloodgroups.php';
require_once '../config/code_states.php';
require_once '../config/code_genders.php';

$conn = prepare_new_connection();

$total_banks = get_total_banks();
$bg_bank_data_map = [];
$state_id_bank_data = [];

$total_donors = get_total_donor_count();
$bg_donor_data_map = [];
$state_id_donor_data = [];
$gender_donor_data = [];

foreach ($states as $st_key => $st_values) {
    $state_id_bank_data[$states[$st_key]] = get_banks_count_with_state_id($st_key);
}

foreach ($blood_groups as $bg_key => $bg_values) {
    $bg_bank_data_map[$blood_groups[$bg_key]] = get_total_blood_stock_by_blood_group($bg_key);
}

foreach ($blood_groups as $bg_key => $bg_values) {
    $bg_donor_data_map[$blood_groups[$bg_key]] = get_donor_count_with_blood_group($bg_key);
}

foreach ($states as $st_key => $st_values) {
    $state_id_donor_data[$states[$st_key]] = get_donor_count_by_state_id($st_key);
}

foreach ($genders as $gn_key => $gn_values) {
    $gender_donor_data[$genders[$gn_key]] = get_donor_count_with_gender($gn_key);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Full Reports Dashboard</title>
    <!-- Tailwind CSS-->
    <link href="/src/css/common.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<!-- Header/Navigation -->
<header class="shadow-sm py-4" style="background: #093c66ff;">
    <div class="container mx-auto px-4 flex justify-between items-center">
        <img src="/src/assets/logo.png" alt="Blood Bank Logo" class="img-fluid" style="height: 30px; width: 30px;">
        <a href="#" class="text-2xl font-bold items-center text-white">BBMS - REPORTS </a>
        <nav>
            <ul class="flex space-x-6">
                <li><a href="/index.php" class="text-white hover:text-blue-200 font-medium">Home</a>
                </li>
                <li><a href="/src/pages/auth/registeruser.php"
                        class="text-white hover:text-blue-200 font-medium">Register Donor</a>
                </li>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { ?>
                    <li><a href="/src/pages/search.php" class="text-white hover:text-blue-200 font-medium">Search</a>
                    </li>
                    <li><a href="/src/pages/auth/logout.php"
                            class="text-orange-500 hover:text-orange-200 font-bold">Logout</a>
                    </li>
                    <?php if ($_SESSION['role'] == "admin") { ?>
                        <li><a href="/src/pages/admin/dashboard.php"
                                class="text-orange-500 hover:text-orange-200 font-bold">Admin
                                Dashboard</a>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </nav>
    </div>
</header>

<body class="bg-gradient-to-br from-purple-50 to-pink-50 min-h-screen text-gray-900">


    <div class="max-w-7xl mx-auto p-6">
        <!-- Top summary -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 justify-center text-center">
            <div class="bg-white rounded-2xl p-4 shadow w-48">
                <div class="text-sm text-gray-500">Total Donors</div>
                <div class="text-2xl font-bold text-purple-700"><?= $total_donors ?></div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow w-48">
                <div class="text-sm text-gray-500">Total Banks</div>
                <div class="text-2xl font-bold text-amber-600"><?= $total_banks ?></div>
            </div>
        </section>
    </div>

    <div class="grid grid-cols-2 gap-6 max-w-7xl mx-auto p-6">

        <div class="col-span-1">
            <!-- Blood stock by group -->
            <div class="chart-container">
                <h2 class="font-bold text-blue-500">Blood Stock by Blood Group</h2>
                <canvas id="bloodStockChart"></canvas>
            </div>
        </div>

        <div class="col-span-1">
            <!-- Donors by blood group -->
            <div class="chart-container">
                <h2 class="font-bold text-blue-500">Donors by Blood Group</h2>
                <canvas id="donorGroupChart"></canvas>
            </div>
        </div>

        <div class="max-w-3xl col-span-1">
            <!-- Banks by state -->
            <div class="chart-container">
                <h2 class="font-bold text-blue-500">Banks by State</h2>
                <canvas id="banksStateChart"></canvas>
            </div>
        </div>

        <div class="max-w-3xl col-span-1">
            <!-- Donors by state -->
            <div class="chart-container">
                <h2 class="font-bold text-blue-500">Donors by State</h2>
                <canvas id="donorsStateChart"></canvas>
            </div>
        </div>


        <div class="max-w-3xl col-span-1">
            <!-- Donors by gender -->
            <div class="chart-container">
                <h2 class="font-bold text-blue-500">Donors by Gender</h2>
                <canvas id="genderChart"></canvas>
            </div>
        </div>

    </div>

    <script>
        // Blood stock by group
        new Chart(document.getElementById("bloodStockChart"), {
            type: "bar",
            data: {
                labels: <?php echo json_encode(array_keys($bg_bank_data_map)); ?>,
                datasets: [{
                    label: "Units Available",
                    data: <?php echo json_encode(array_values($bg_bank_data_map)); ?>,
                    backgroundColor: "rgba(255, 99, 132, 0.6)"
                }]
            }
        });

        // Donors by group
        new Chart(document.getElementById("donorGroupChart"), {
            type: "bar",
            data: {
                labels: <?php echo json_encode(array_keys($bg_donor_data_map)); ?>,
                datasets: [{
                    label: "Donors",
                    data: <?php echo json_encode(array_values($bg_donor_data_map)); ?>,
                    backgroundColor: "rgba(54, 162, 235, 0.6)"
                }]
            }
        });

        // Banks by state
        new Chart(document.getElementById("banksStateChart"), {
            type: "pie",
            data: {
                labels: <?php echo json_encode(array_keys($state_id_bank_data)); ?>,
                datasets: [{
                    label: "Banks",
                    data: <?php echo json_encode(array_values($state_id_bank_data)); ?>,
                    backgroundColor: [
                        "#FF6384", "#36A2EB", "#FFCE56", "#8E44AD", "#2ECC71",
                        "#E67E22", "#1ABC9C", "#D35400", "#7F8C8D", "#9B59B6",
                        "#3498DB", "#27AE60", "#F39C12", "#C0392B", "#BDC3C7",
                        "#16A085", "#2980B9", "#8E44AD", "#2C3E50", "#F1C40F",
                        "#E74C3C", "#95A5A6", "#34495E", "#E67E22", "#1ABC9C",
                        "#D35400", "#7D3C98", "#45B39D"
                    ]
                }]
            }
        });

        // Donors by state
        new Chart(document.getElementById("donorsStateChart"), {
            type: "doughnut",
            data: {
                labels: <?php echo json_encode(array_keys($state_id_donor_data)); ?>,
                datasets: [{
                    label: "Donors",
                    data: <?php echo json_encode(array_values($state_id_donor_data)); ?>,
                    backgroundColor: [
                        "#FF6384", "#36A2EB", "#FFCE56", "#8E44AD", "#2ECC71",
                        "#E67E22", "#1ABC9C", "#D35400", "#7F8C8D", "#9B59B6",
                        "#3498DB", "#27AE60", "#F39C12", "#C0392B", "#BDC3C7",
                        "#16A085", "#2980B9", "#8E44AD", "#2C3E50", "#F1C40F",
                        "#E74C3C", "#95A5A6", "#34495E", "#E67E22", "#1ABC9C",
                        "#D35400", "#7D3C98", "#45B39D"
                    ]
                }]
            }
        });

        // Donors by gender
        new Chart(document.getElementById("genderChart"), {
            type: "bar",
            data: {
                labels: <?php echo json_encode(array_keys($gender_donor_data)); ?>,
                datasets: [{
                    label: "Donors",
                    data: <?php echo json_encode(array_values($gender_donor_data)); ?>,
                    backgroundColor: "rgba(153, 102, 255, 0.6)"
                }]
            }
        });
    </script>
</body>

</html>
