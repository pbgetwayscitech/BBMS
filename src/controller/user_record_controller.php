<?php

/**
 * Searches and retrieves records from a donor-specific table based on a query and filter.
 *
 * This function allows searching through a donor's records table using a specific filter
 * (e.g., record_type, blood_group) or across all columns if the filter is set to 'all'.
 * It returns the matching records as an array of UniqueIdRecord objects.
 *
 * @param string $table_name The name of the donor-specific records table.
 * @param string $query The search string to match against the table fields.
 * @param string $filter The column to filter by, or 'all' to search across all columns.
 * @return UniqueIdRecord[] An array of UniqueIdRecord objects matching the search criteria.
 */
function find_records($table_name, $query, $filter)
{
    require_once __DIR__ . '../../config/db.php';
    require_once __DIR__ . '../../config/filter_records.php';
    require_once __DIR__ . '../../model/UniqueIDRecords.php';

    $query = "%" . trim($query) . "%";
    $conn = prepare_new_connection();
    $records = [];
    global $filter_records;

    if ($filter != 'all' && in_array($filter, array_keys($filter_records))) {
        //specific filter
        $sql_query = "SELECT record_number, record_type, blood_group, note, bank_id, date
                      FROM $table_name
                      WHERE $filter LIKE ?";
        $stmt = $conn->prepare($sql_query);
        $stmt->bind_param("s", $query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } elseif ($filter == 'all') {
        $sql_query = 'SELECT record_number, record_type , blood_group, note, bank_id, date from ' . $table_name . ' WHERE
        record_number LIKE ? OR record_type LIKE ? OR blood_group LIKE ? OR note LIKE ? OR bank_id LIKE ?';
        $stmt = $conn->prepare($sql_query);
        $stmt->bind_param("sssss", $query, $query, $query, $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } elseif (trim($query) == '') {
        $sql_query = 'SELECT record_number, record_type , blood_group, note, bank_id, date from ' . $table_name;
        $stmt = $conn->prepare($sql_query);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        //invalid filter
        $conn->close();
        return [];
    }

    $conn->close();

    // Convert result rows into UniqueIdRecord objects
    while ($row = $result->fetch_assoc()) {
        $record = new UniqueIdRecord(
            $row['record_number'],
            $row['record_type'],
            $row['blood_group'],
            $row['note'],
            $row['bank_id'],
            $row['date']
        );
        $records[] = $record;
    }

    return $records;
}

/**
 * Adds a new record to a donor's specific records table.
 *
 * This function inserts a new entry into the provided donor-specific table using the details
 * from a UniqueIdRecord object, including record type, blood group, note, bank ID, and date.
 *
 * @param string $table_name The name of the donor-specific records table where the data will be inserted.
 * @param UniqueIdRecord $rec An object containing the record details to be added.
 * @return bool Returns true if the insertion is successful, false otherwise.
 */
function add_data_to_user_record($table_name, UniqueIdRecord $rec)
{
    require_once __DIR__ . '../../config/db.php';
    require_once __DIR__ . '../../model/UniqueIDRecords.php';

    $conn = prepare_new_connection();
    if (!$conn) {
        return false;
    }

    $record_type = $rec->getRecordType();
    $blood_group = $rec->getBloodGroup();
    $note = $rec->getNote();
    $bank_id = $rec->getBankId();
    $date = $rec->getDate();

    $sql_query = "INSERT INTO $table_name ( record_type, blood_group, note, bank_id, date)VALUES(?,?,?,?,?)";
    $stmt = $conn->prepare($sql_query);
    $stmt->bind_param("sssss", $record_type, $blood_group, $note, $bank_id, $date);
    $m = $stmt->execute();
    $stmt->close();

    $conn->close();

    return $m;
}


?>

