<?php

/**
 * Counts the number of blood banks in a specific state.
 *
 * This function queries the `blood_banks` table to find all banks
 * with the given state ID and returns the total count.
 *
 * @param int $state_id The ID of the state to count blood banks for.
 * @return int The number of blood banks in the specified state.
 */
function get_banks_count_with_state_id($state_id)
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();
    $stmt = $conn->prepare("SELECT bank_id FROM blood_banks WHERE state_id = $state_id");
    $stmt->execute();
    $result = $stmt->get_result();
    $rows_count = $result->num_rows;
    return $rows_count;
}

/**
 * Calculates the total stock of a specific blood group across all blood banks.
 *
 * This function sums the quantities of the specified blood group
 * from all records in the `blood_banks` table where the stock is greater than zero.
 *
 * @param string $blood_group The blood group column to sum (e.g., 'a', 'ap', 'b', 'bp', 'o', 'op', 'ab', 'abp').
 * @return int The total quantity of the specified blood group across all banks.
 */
function get_total_blood_stock_by_blood_group($blood_group)
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();

    $stmt = $conn->prepare("SELECT $blood_group AS bg FROM blood_banks WHERE $blood_group > 0");
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;

    while ($data = $result->fetch_assoc()) {
        $total += $data['bg'];
    }

    return $total;
}

/**
 * Retrieves the total number of blood banks in the system.
 *
 * This function queries the `blood_banks` table and returns
 * the total count of bank records.
 *
 * @return int The total number of blood banks.
 */
function get_total_banks()
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();
    $stmt = $conn->prepare("SELECT bank_id FROM blood_banks");
    $stmt->execute();
    $result = $stmt->get_result();
    $rows_count = $result->num_rows;
    return $rows_count;
}

/**
 * Counts the number of donors in a specific state.
 *
 * This function queries the `gen_donors` table to find all donors
 * with the given state code and returns the total count.
 *
 * @param int $state_id The state code to count donors for.
 * @return int The number of donors in the specified state.
 */
function get_donor_count_by_state_id($state_id)
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();
    $stmt = $conn->prepare("SELECT donor_id FROM gen_donors WHERE state_code = ?");
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows_count = $result->num_rows;
    return $rows_count;
}

/**
 * Counts the number of donors based on gender.
 *
 * This function queries the `gen_donors` table to find all donors
 * matching the specified gender and returns the total count.
 *
 * @param string $gender The gender to filter donors by (e.g., 'Male', 'Female', 'Other').
 * @return int The number of donors with the specified gender.
 */
function get_donor_count_with_gender($gender)
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();
    $stmt = $conn->prepare("SELECT donor_id FROM gen_donors WHERE gender = ?");
    $stmt->bind_param("s", $gender);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows_count = $result->num_rows;
    return $rows_count;
}

/**
 * Counts the number of donors with a specific blood group.
 *
 * This function queries the `gen_donors` table to find all donors
 * who have the specified blood group and returns the total count.
 *
 * @param string $blood_group The blood group to filter donors by (e.g., 'A+', 'O-', etc.).
 * @return int The number of donors with the specified blood group.
 */
function get_donor_count_with_blood_group($blood_group)
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();
    $stmt = $conn->prepare("SELECT donor_id FROM gen_donors WHERE blood_group = ?");
    $stmt->bind_param("s", $blood_group);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows_count = $result->num_rows;
    return $rows_count;
}

/**
 * Retrieves the total number of registered donors.
 *
 * This function queries the `gen_donors` table and returns
 * the total count of all donor records in the system.
 *
 * @return int The total number of donors.
 */
function get_total_donor_count()
{
    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();
    $stmt = $conn->prepare("SELECT donor_id FROM gen_donors");
    $stmt->execute();
    $result = $stmt->get_result();
    $rows_count = $result->num_rows;
    return $rows_count;

}


?>

