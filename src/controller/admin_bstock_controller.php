<?php

/**
 * Adds a new blood donation record to the blood stock table.
 *
 * This function inserts details of a blood donation into the specified
 * blood stock table using data from the BankIdBloodStock object. It runs
 * inside a database transaction to ensure data integrity, rolling back
 * if an error occurs.
 *
 * @param string $blood_stock_tablename The name of the database table for storing blood stock records.
 * @param BankIdBloodStock $stock_data The blood stock data object containing donation details.
 * @return bool True if the donation record was successfully added, false on failure.
 */
function add_new_donation($blood_stock_tablename, BankIdBloodStock $stock_data)
{
    require_once __DIR__ . '../../config/db.php';
    require_once __DIR__ . '../../model/bankIDbloodStock.php';
    require_once __DIR__ . '../../config/code_bloodgroups.php';
    global $blood_groups;

    $conn = prepare_new_connection();

    if (!$conn) {
        return false;
    }

    $donor_id = $stock_data->getDonorId();
    $bank_id = $stock_data->getBankId();
    $donation_date = $stock_data->getDonationDate();
    $blood_group = $stock_data->getBloodGroup();
    $expiry_date = $stock_data->getExpiaryDate();
    $note = $stock_data->getNote();
    $stock_status = $stock_data->getStockStatus();
    $date = $stock_data->getDonationDate();

    if (!array_key_exists($blood_group, $blood_groups)) {
        return false;
    }

    try {
        $conn->begin_transaction();

        // Insert into blood stock table
        $stmt = $conn->prepare("
            INSERT INTO {$blood_stock_tablename}
            (donor_id, blood_group, donation_date, expiary_date, note, bank_id, stock_status, stock_status_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("issssiss", $donor_id, $blood_group, $donation_date, $expiry_date, $note, $bank_id, $stock_status, $date);
        $stmt->execute();
        $stmt->close();
        $conn->commit();
        $conn->close();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        return false;
    }
}

/**
 * Updates the status of a blood stock record.
 *
 * This function changes the stock status of a specific blood unit
 * identified by its stock ID. Only "utilised" or "discarded" statuses
 * are allowed. The stock_status_date is automatically updated to the
 * current timestamp.
 *
 * @param string $blood_stock_tablename The name of the blood stock database table.
 * @param int $stock_id The Stock ID of the blood stock record to update.
 * @param string $new_status The new status value ("utilised" or "discarded").
 * @return bool True if the update was successful, false otherwise (e.g., invalid input or DB failure).
 */
function update_blood_Stock_status($blood_stock_tablename, $stock_id, $new_status)
{
    require_once __DIR__ . '../../config/db.php';

    if ($blood_stock_tablename == '' || $stock_id == '' || !in_array($new_status, ['utilised', 'discarded'], true)) {
        return false;
    }

    $conn = prepare_new_connection();
    if (!$conn) {
        return false;
    }

    // Check if stock_id exists
    $stmt_check = $conn->prepare("SELECT * FROM {$blood_stock_tablename} WHERE stock_id = ? LIMIT 1");
    $stmt_check->bind_param("i", $stock_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if (!$result_check || mysqli_num_rows($result_check) === 0) {
        $stmt_check->close();
        $conn->close();
        return false;
    }
    $stmt_check->close();


    $stmt = $conn->prepare("
            UPDATE {$blood_stock_tablename}
            SET stock_status = ?, stock_status_date = NOW()
            WHERE stock_id = ?
        ");
    $stmt->bind_param("si", $new_status, $stock_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    if (!$result) {
        return false;
    }

    return true;
}

/**
 * Searches blood stock records from the database.
 *
 * This function retrieves blood stock records based on a search query
 * and filter.
 * - If the filter is "all", the query is applied to all searchable fields.
 * - If the filter matches a valid column in the blood stock table, the
 *   search is restricted to that column.
 * - If the filter is invalid, all records are returned.
 *
 * Results are returned as an array of BankIdBloodStock objects.
 *
 * @param string $blood_stock_tablename The name of the blood stock table in the database.
 * @param string $search_query The search keyword or value to match.
 * @param string $search_filter The column to filter by, or "all" to search across all fields.
 * @return BankIdBloodStock[]|null An array of matching blood stock objects, or null if the database connection fails.
 */
function search_blood_stock($blood_stock_tablename, $search_query, $search_filter)
{

    require_once __DIR__ . '../../config/db.php';
    require_once __DIR__ . '../../model/bankIDbloodStock.php';
    require_once __DIR__ . '../../config/filter_searchBstock.php';

    global $filter_searchBstock;

    $conn = prepare_new_connection();
    if (!$conn) {
        return null;
    }

    $search_query = "%" . $search_query . "%";
    $records = [];

    if ($search_filter === 'all') {
        $stmt = $conn->prepare("
            SELECT stock_id, donor_id, blood_group, bank_id, note, donation_date, expiary_date, stock_status, stock_status_date
            FROM {$blood_stock_tablename} WHERE stock_id = ? OR blood_group LIKE ? OR bank_id = ? OR note LIKE ? OR
            donation_date LIKE ? OR expiary_date LIKE ? OR stock_status LIKE ? OR stock_status_date LIKE ?
            ORDER BY expiary_date
        ");
        $stmt->bind_param("ssssssss", $search_query, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

    } else if ($search_filter != 'all' && in_array($search_filter, array_keys($filter_searchBstock), true)) {
        $stmt = $conn->prepare("
            SELECT stock_id, donor_id, blood_group, note, bank_id, donation_date, expiary_date, stock_status, stock_status_date
            FROM {$blood_stock_tablename}
            WHERE {$search_filter} LIKE ?
            ORDER BY expiary_date
        ");
        $stmt->bind_param("s", $search_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

    } else {
        $stmt = $conn->prepare("
            SELECT stock_id, donor_id, blood_group, bank_id, note, donation_date, expiary_date, stock_status, stock_status_date
            FROM " . $blood_stock_tablename . " ORDER BY expiary_date
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    $conn->close();

    // Convert result rows into UniqueIdRecord objects
    while ($row = $result->fetch_assoc()) {
        $record = new BankIdBloodStock(
            $row['stock_id'],
            $row['donor_id'],
            $row['bank_id'],
            $row['donation_date'],
            $row['blood_group'],
            $row['expiary_date'],
            $row['note'],
            $row['stock_status'],
            $row['stock_status_date']
        );
        $records[] = $record;
    }
    return $records;
}




?>

