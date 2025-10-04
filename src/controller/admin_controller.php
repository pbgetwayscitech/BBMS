<?php

/**
 * Retrieves a blood bank admin's information by email.
 *
 * This function searches the `blood_banks` table for a record with the
 * specified email ID. If found, it returns a BloodBank object containing
 * the admin's details, including blood stock counts for each blood group.
 * If no record is found or the query fails, it returns null.
 *
 * @param string $email_id The email address of the blood bank admin to search for.
 * @return BloodBank|null Returns a BloodBank object if found, or null if no matching record exists or on failure.
 */
function search_admin_with_email($email_id)
{
    require_once __DIR__ . "../../config/db.php";
    require_once __DIR__ . "../../model/bloodBank.php";

    $conn = prepare_new_connection();

    $stmt = mysqli_prepare($conn, "SELECT bank_id, bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email,
    password_hash, a , ap, b, bp , ab , abp , o , op FROM blood_banks WHERE bank_email = ? LIMIT 1");

    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "s", $email_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $records = mysqli_fetch_assoc($result);

    if (!$records) {
        return null;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $admin_inf = new BloodBank(
        bank_id: $records['bank_id'],
        bank_name: $records['bank_name'],
        pincode: $records['pincode'],
        state_id: $records['state_id'],
        bank_owner: $records['bank_owner'],
        address: $records['address'],
        phone_number: $records['phone_number'],
        bank_email: $records['bank_email'],
        password_hash: $records['password_hash'],
        a_stock: $records['a'],
        ap_stock: $records['ap'],
        b_stock: $records['b'],
        bp_stock: $records['bp'],
        o_stock: $records['o'],
        op_stock: $records['op'],
        ab_stock: $records['ab'],
        abp_stock: $records['abp']
    );

    return $admin_inf;

}

/**
 * Retrieves the quantity of a specific blood group for a blood bank by email and ID.
 *
 * This function queries the `blood_banks` table for the specified bank ID and email,
 * and returns the quantity of the requested blood group. If the query fails or no record
 * is found, it returns false.
 *
 * @param string $email_id The email address of the blood bank.
 * @param int $bank_id The ID of the blood bank.
 * @param string $blood_group The blood group to retrieve (e.g., 'a', 'ap', 'b', 'bp', 'o', 'op', 'ab', 'abp').
 * @return int|false The quantity of the specified blood group, or false on failure.
 */
function get_blood_group_qty_for_bank_email($email_id, $bank_id, $blood_group)
{
    require_once __DIR__ . "../../config/db.php";
    require_once __DIR__ . "../../model/bloodBank.php";

    $conn = prepare_new_connection();

    $stmt = mysqli_prepare($conn, "SELECT {$blood_group} as bg FROM blood_banks WHERE bank_id = ? AND bank_email = ?  LIMIT 1");

    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "is", $bank_id, $email_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $records = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $records['bg'];
}


/**
 * Checks if a blood bank admin already exists in the database.
 *
 * This function searches the `blood_banks` table for a record matching
 * the given bank name, email, or phone number. It returns true if a
 * matching admin exists, and false otherwise. Also returns false if
 * the query fails.
 *
 * @param string $bank_name The name of the blood bank to check.
 * @param string $email_id The email address of the admin to check.
 * @param string $phone_number The phone number of the admin to check.
 * @return bool True if an admin with any of the given details exists, false otherwise.
 */
function does_admin_exist($bank_name, $email_id, $phone_number)
{
    require_once __DIR__ . "../../config/db.php";

    $conn = prepare_new_connection();

    $stmt = mysqli_prepare($conn, "SELECT bank_name, bank_email, phone_number FROM blood_banks WHERE bank_name = ? OR bank_email = ? OR phone_number = ?
                                   LIMIT 1");
    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "sss", $bank_name, $email_id, $phone_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result->num_rows > 0) {
        // user exists
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return true;
    } else {
        // user does not exist
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return false;
    }
}

/**
 * Registers a new blood bank in the database.
 *
 * This function inserts a new blood bank record into the `blood_banks` table
 * using the details provided by a BloodBank object. All blood group stock
 * fields are initialized to zero. Returns true if the registration is successful,
 * or false if the query fails.
 *
 * @param BloodBank $blood_bank The BloodBank object containing details of the new blood bank.
 * @return bool True on successful insertion, false on failure.
 */
function register_blood_bank(BloodBank $blood_bank)
{
    require_once __DIR__ . "../../model/bloodBank.php";
    require_once __DIR__ . "../../config/db.php";

    $conn = prepare_new_connection();
    $bv = 0;

    $bank_name = $blood_bank->getBankName();
    $pincode = $blood_bank->getPincode();
    $state_id = $blood_bank->getStateId();
    $bank_owner = $blood_bank->getBankOwner();
    $address = $blood_bank->getAddress();
    $phone_number_m = $blood_bank->getPhoneNumber();
    $bank_email = $blood_bank->getBankEmail();
    $hashed_password = $blood_bank->getPasswordHash();

    // Prepare and bind parameters for insertion
    $stmt = $conn->prepare("INSERT INTO blood_banks (bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email, password_hash,
                    a, ap, b , bp, o ,op, ab , abp)  VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        // Query preparation failed
        mysqli_close($conn);
        return false;
    }

    $stmt->bind_param(
        "siississiiiiiiii",
        $bank_name,
        $pincode,
        $state_id,
        $bank_owner,
        $address,
        $phone_number_m,
        $bank_email,
        $hashed_password,
        $bv,
        $bv,
        $bv,
        $bv,
        $bv,
        $bv,
        $bv,
        $bv
    );

    $vu = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $vu;
}

/**
 * Creates blood stock and blood request tables for a new blood bank.
 *
 * This function dynamically creates two tables specific to a blood bank:
 * 1. `<bank_id><phone_number>_blood_stock` – stores individual blood donation records.
 * 2. `<bank_id><phone_number>_blood_request` – stores blood requests associated with the bank.
 *
 * Before creating each table, the function checks if it already exists in the database.
 * Foreign key constraints link blood stock to donors and blood requests to the blood bank.
 *
 * @param int $bank_id The unique ID of the blood bank.
 * @param string $phone_number The phone number of the blood bank, used to create unique table names.
 * @return bool Returns true after attempting to create the tables. False may be returned if table checks/preparations fail.
 */
function create_other_bank_related_tables($bank_id, $phone_number)
{
    require_once __DIR__ . "../../config/db.php";

    $conn = prepare_new_connection();

    $table_name = '' . $bank_id . $phone_number . '_blood_stock';
    $check_stmt_for_tables = $conn->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?");

    if (!$check_stmt_for_tables) {
        return false;
    }

    $check_stmt_for_tables->bind_param("s", $table_name);
    $check_stmt_for_tables->execute();
    $check_result = $check_stmt_for_tables->get_result();

    if ($check_result->num_rows === 0) {
        //needs to create table  ...
        $stock_tb = $conn->prepare("CREATE TABLE " . $table_name . "(stock_id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, donor_id INT(11) NOT NULL, bank_id INT(11) NOT NULL,
            donation_date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP, blood_group ENUM('a' , 'ap' , 'b' , 'bp' , 'ab' , 'abp' , 'o' , 'op') NOT NULL, expiary_date DATE NOT NULL,
            note TEXT NOT NULL, stock_status ENUM('preserved' , 'utilised' , 'discarded') NOT NULL, stock_status_date DATE NOT NULL, FOREIGN KEY (donor_id) REFERENCES gen_donors(donor_id),
            FOREIGN KEY (bank_id) REFERENCES blood_banks(bank_id)) ");

        $result = $stock_tb->execute();
    }
    $check_stmt_for_tables->close();

    $bdrq_table_name = '' . $bank_id . $phone_number . '_blood_request';
    $check_tb_blood_requests = $conn->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?");

    if (!$check_tb_blood_requests) {
        return false;
    }

    $check_tb_blood_requests->bind_param("s", $bdrq_table_name);
    $check_tb_blood_requests->execute();
    $chk_tbrq_result = $check_tb_blood_requests->get_result();

    if ($chk_tbrq_result->num_rows === 0) {

        $tbrq = $conn->prepare("CREATE TABLE " . $bdrq_table_name . "( request_id INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL, requested_by TEXT NOT NULL, requested_by_id INT(11) NOT NULL,
            requested_blood_group ENUM('a', 'ap' , 'b' , 'bp' , 'ab' , 'abp' , 'o' , 'op') NOT NULL, requested_for INT(11) NOT NULL, bank_id INT(11) NOT NULL, requested_on DATE NOT NULL DEFAULT CURRENT_TIMESTAMP,
            status ENUM('requested', 'fulfilled', 'discarded') NOT NULL, note TEXT NOT NULL, FOREIGN KEY (bank_id) REFERENCES blood_banks(bank_id))");

        $res = $tbrq->execute();
    }
    $check_tb_blood_requests->close();
    $conn->close();

    return true;
}

/**
 * Updates the quantity of a specific blood group for a blood bank.
 *
 * This function updates a particular blood group stock field in the `blood_banks` table
 * for the specified bank ID and phone number. Only valid and non-empty parameters
 * are allowed.
 *
 * @param int $bank_id The unique ID of the blood bank.
 * @param string $bank_phone_number The phone number of the blood bank.
 * @param string $blood_group The blood group column to update (e.g., 'a', 'ap', 'b', 'bp', 'o', 'op', 'ab', 'abp').
 * @param int $new_value The new stock quantity for the specified blood group.
 * @return bool True if the update was executed successfully, false if input validation fails.
 */
function update_blood_stock_param($bank_id, $bank_phone_number, $blood_group, $new_value)
{

    require_once __DIR__ . '../../config/db.php';
    $conn = prepare_new_connection();

    if (empty($new_value) || empty($blood_group) || empty($bank_phone_number) || empty($bank_id)) {
        return false;
    }

    $stmt = $conn->prepare("
            UPDATE blood_banks
            SET {$blood_group} = ?
            WHERE bank_id = ? AND phone_number = ?
        ");
    $stmt->bind_param("iis", $new_value, $bank_id, $bank_phone_number);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();

    return true;

}

?>

