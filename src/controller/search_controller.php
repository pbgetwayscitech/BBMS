<?php

/**
 * Searches blood banks based on state, blood group availability, and an optional query.
 *
 * This function retrieves blood bank records from the `blood_banks` table that:
 * - Are located in the specified state.
 * - Have a positive stock of the specified blood group.
 * - Optionally match the provided search query against multiple bank fields (bank ID, name, pincode, owner, address, phone, email).
 *
 * Results are returned as an array of BloodBank objects.
 *
 * @param string $search_query Optional search keyword to filter banks by various fields. Can be empty.
 * @param string $blood_group The blood group to filter banks by (e.g., 'a', 'ap', 'b', 'bp', 'o', 'op', 'ab', 'abp').
 * @param int $state_id The state ID to filter banks by.
 * @return BloodBank[]|null An array of matching BloodBank objects, or null if the state or blood group is invalid, or if the database connection fails.
 */
function search_bank_with_criteria($search_query, $blood_group, $state_id)
{

    require_once __DIR__ . '../config/db.php';
    require_once __DIR__ . '../config/code_bloodgroups.php';
    require_once __DIR__ . '../config/code_states.php';
    require_once __DIR__ . '../model/bloodBank.php';
    global $blood_groups;
    global $states;

    if (!array_key_exists($blood_group, $blood_groups) || !array_key_exists($state_id, $states)) {
        return null;
    }

    $conn = prepare_new_connection();
    if (!$conn) {
        return null;
    }

    $records = [];

    if ($search_query == "") {
        $stmt = mysqli_prepare($conn, "SELECT bank_id, bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email,
        a , ap, b, bp , ab , abp , o , op FROM blood_banks WHERE state_id = ? AND {$blood_group} > 0 ");
        $stmt->bind_param("i", $state_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $search_query = '%' . $search_query . '%';
        $stmt = mysqli_prepare($conn, "SELECT bank_id, bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email,
        a , ap, b, bp , ab , abp , o , op FROM blood_banks WHERE state_id = ? AND {$blood_group} > 0  AND bank_id LIKE ? OR bank_name LIKE ? OR pincode LIKE ? OR bank_owner LIKE ?
        OR address LIKE ? OR phone_number LIKE ? OR bank_email LIKE ?");
        $stmt->bind_param("isssssss", $state_id, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    $conn->close();

    while ($row = $result->fetch_assoc()) {
        $record = new BloodBank(
            $row['bank_id'],
            $row['bank_name'],
            $row['pincode'],
            $row['state_id'],
            $row['bank_owner'],
            $row['address'],
            $row['phone_number'],
            $row['bank_email'],
            ' ',
            $row['a'],
            $row['ap'],
            $row['b'],
            $row['bp'],
            $row['ab'],
            $row['abp'],
            $row['o'],
            $row['op'],
        );
        $records[] = $record;
    }

    return $records;
}

/**
 * Adds a new blood request record to the specified requests table.
 *
 * This function inserts a blood request using the details provided
 * by a BankIdBloodRequest object. It includes information about
 * who requested the blood, the blood group, the intended recipient,
 * the associated blood bank, request date, notes, and status.
 *
 * @param string $requests_table The name of the database table to store blood requests.
 * @param BankIdBloodRequest $req The BankIdBloodRequest object containing request details.
 * @return bool True if the request was successfully added, false if the insertion fails or the database connection fails.
 */
function add_blood_request($requests_table, BankIdBloodRequest $req)
{
    require_once __DIR__ . '../config/db.php';
    require_once __DIR__ . '../model/bankIdBloodRequest.php';

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
    if (!$result) {
        return false;
    }

    return true;
}

/**
 * Adds a new record to a user's record table.
 *
 * This function inserts data from a UniqueIdRecord object into the specified
 * user record table. The record includes the record number, type, blood group,
 * notes, associated bank ID, and the date of the record.
 *
 * @param string $table_name The name of the user's record table where the data will be inserted.
 * @param UniqueIdRecord $rec The UniqueIdRecord object containing the record details.
 * @return bool True if the record was successfully added, false if the insertion fails or the database connection fails.
 */
function add_data_to_user_record($table_name, UniqueIdRecord $rec)
{
    require_once __DIR__ . '../config/db.php';
    require_once __DIR__ . '../model/UniqueIDRecords.php';

    $conn = prepare_new_connection();
    if (!$conn) {
        return false;
    }

    $record_number = $rec->getRecordNumber();
    $record_type = $rec->getRecordType();
    $blood_group = $rec->getBloodGroup();
    $note = $rec->getNote();
    $bank_id = $rec->getBankId();
    $date = $rec->getDate();

    $sql_query = "INSERT INTO $table_name (record_number, record_type, blood_group, note, bank_id, date)VALUES(?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql_query);
    $stmt->bind_param("ssssss", $record_number, $record_type, $blood_group, $note, $bank_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $conn->close();
    if (!$result) {
        return false;
    }
    return true;
}


?>

