<?php

use PHPUnit\Event\Code\Throwable;
use function PHPUnit\Framework\throwException;

/**
 * Adds a new blood request record to the database.
 *
 * This function inserts details of a blood request into the specified
 * requests table. It uses prepared statements for secure insertion and
 * retrieves request data through the BankIdBloodRequest object.
 *
 * @param string $requests_table The name of the database table for storing blood requests.
 * @param BankIdBloodRequest $req The blood request data object containing request details.
 * @return bool True if the request was successfully inserted, false on failure.
 */
function add_blood_request($requests_table, BankIdBloodRequest $req)
{
    require_once __DIR__ . '../../config/db.php';
    require_once __DIR__ . '../../model/bankIdBloodRequest.php';
    require_once __DIR__ . '../../config/code_bloodgroups.php';
    global $blood_groups;

    $conn = prepare_new_connection();

    if (!$conn) {
        return false;
    }

    $requested_by = $req->getRequestedBy();
    $requested_by_id = $req->getRequestedById();
    $requested_blood_group = $req->getRequestedBloodGroup();
    $requested_for = $req->getRequestedFor();
    $bank_id = $req->getBankId();
    $requested_on = $req->getRequestedOn();
    $note = $req->getNote();
    $status = $req->getStatus();


    // .......
    //blood group check

    if (!key_exists($requested_blood_group, $blood_groups)) {
        return false;
    }


    $stmt = $conn->prepare("
            INSERT INTO {$requests_table}
            (requested_by, requested_by_id, requested_blood_group, requested_for, bank_id, requested_on, note, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("sississs", $requested_by, $requested_by_id, $requested_blood_group, $requested_for, $bank_id, $requested_on, $note, $status);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $result;
}

/**
 * Updates the status of a blood request in the database.
 *
 * This function changes the status of a specific blood request identified
 * by its request ID and associated bank ID. It ensures that only the correct
 * bank’s request record is updated.
 *
 * @param string $requests_table The name of the database table containing blood requests.
 * @param int $request_id The unique ID of the blood request to update.
 * @param string $new_status The new status value to assign to the request.
 * @param int $bank_id The ID of the bank associated with the request.
 * @return bool True if the status was successfully updated, false on failure.
 */
function update_request_status($requests_table, $request_id, $new_status, $bank_id)
{

    require_once __DIR__ . '../../config/db.php';

    $conn = prepare_new_connection();
    if (!$conn) {
        return false;
    }

    $check_stmt = $conn->prepare("SELECT * from {$requests_table} WHERE request_id = ? LIMIT 1");
    $check_stmt->bind_param("i", $request_id);
    $check_stmt->execute();
    $mresult = $check_stmt->get_result();
    if (!$mresult || mysqli_num_rows($mresult) === 0) {
        $check_stmt->close();
        $conn->close();
        return false;
    }
    $check_stmt->close();

    $stmt = $conn->prepare("
            UPDATE {$requests_table}
            SET status = ?
            WHERE request_id = ? AND bank_id = ?
        ");

    $stmt->bind_param("sii", $new_status, $request_id, $bank_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    if (!$result) {
        return false;
    }

    return true;
}

/**
 * Searches blood request records from the database.
 *
 * This function retrieves blood request records based on a search filter and query.
 * - If the filter is set to "all", the query is matched against all searchable fields.
 * - If the filter matches a valid field, the query is applied only to that field.
 * - If no valid filter is provided, all records are returned.
 *
 * Results are returned as an array of BankIdBloodRequest objects.
 *
 * @param string $requests_table The name of the database table containing blood requests.
 * @param string $search_filter The field to filter by (or "all" to search across all fields).
 * @param string $search_query The search keyword or value to match.
 * @return BankIdBloodRequest[]|null An array of matching request objects, or null if the connection fails.
 */
function search_from_requests($requests_table, $search_filter, $search_query)
{
    require_once __DIR__ . '../../config/db.php';
    require_once __DIR__ . '/../config/filter_searchBrequests.php';
    require_once __DIR__ . '../../model/bankIDBloodRequest.php';
    global $filter_requests;

    if (!isset($filter_requests) || !is_array($filter_requests)) {
        throw new Exception("Filter requests array not loaded properly. ");
    }

    $conn = prepare_new_connection();
    if (!$conn) {
        return null;
    }

    $records = [];
    $search_query = '%' . $search_query . '%';

    if ($search_filter == 'all') {
        $stmt = $conn->prepare(" SELECT request_id, requested_by, requested_by_id,
        requested_blood_group, requested_for, bank_id, requested_on, status, note FROM {$requests_table} WHERE request_id LIKE ? OR
        requested_by LIKE ? OR requested_by_id LIKE ? OR requested_blood_group LIKE ? OR requested_for LIKE ? OR bank_id LIKE ? OR requested_on LIKE ? OR
        status LIKE ? OR note LIKE ? ");
        $stmt->bind_param(
            "sssssssss",
            $search_query,
            $search_query,
            $search_query,
            $search_query,
            $search_query,
            $search_query,
            $search_query,
            $search_query,
            $search_query
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } elseif ($search_filter != 'all' && in_array($search_filter, array_keys($filter_requests), true)) {
        $stmt = $conn->prepare(" SELECT request_id, requested_by, requested_by_id,
        requested_blood_group, requested_for, bank_id, requested_on, status, note FROM {$requests_table} WHERE {$search_filter} LIKE {$search_query}");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $stmt = $conn->prepare(" SELECT request_id, requested_by, requested_by_id,
        requested_blood_group, requested_for, bank_id, requested_on, status, note FROM {$requests_table}");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    $conn->close();

    while ($row = $result->fetch_assoc()) {
        $record = new BankIdBloodRequest(
            $row['bank_id'],
            $row['requested_by'],
            $row['requested_by_id'],
            $row['requested_blood_group'],
            $row['requested_for'],
            $row['bank_id'],
            $row['requested_on'],
            $row['status'],
            $row['note']
        );
        $records[] = $record;
    }

    return $records;

}

?>

