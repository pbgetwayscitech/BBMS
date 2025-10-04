<?php

/**
 * Retrieves a donor's information by email.
 *
 * This function searches the `gen_donors` table for a record matching
 * the specified email ID. If a matching donor is found, it returns
 * a GenDonor object containing all relevant donor details.
 * Returns null if no donor is found or if the query fails.
 *
 * @param string $email_id The email address of the donor to search for.
 * @return GenDonor|null A GenDonor object if a donor is found, or null if no matching record exists.
 */
function search_user_with_email($email_id)
{
    require_once "../../config/db.php";
    require_once "../../model/genDonor.php";

    $conn = prepare_new_connection();

    $stmt = mysqli_prepare($conn, "SELECT donor_id, full_name, password_hash, unique_id,
    email_id, dob, fathers_name, mothers_name, address, phone_number, state_code, pincode, gender, blood_group, disease, notes
                                   FROM gen_donors
                                   WHERE email_id = ?
                                   LIMIT 1");
    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "s", $email_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $records = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if (!$records) {
        // no user found
        return null;
    }

    // build object only when user exists
    $gen_donor = new GenDonor(
        donor_id: $records['donor_id'],
        full_name: $records['full_name'],
        password_hash: $records['password_hash'],
        unique_id: $records['unique_id'],
        email_id: $records['email_id'],
        dob: $records['dob'],
        fathers_name: $records['fathers_name'],
        mothers_name: $records['mothers_name'],
        address: $records['address'],
        phone_number: $records['phone_number'],
        state_code: $records['state_code'],
        pincode: $records['pincode'],
        gender: $records['gender'],
        blood_group: $records['blood_group'],
        disease: $records['disease'],
        notes: $records['notes'],
    );

    return $gen_donor;
}

/**
 * Retrieves a donor's information by donor ID.
 *
 * This function searches the `gen_donors` table for a record matching
 * the specified donor ID. If a matching donor is found, it returns
 * a GenDonor object containing all relevant donor details.
 * Returns null if no donor is found, the donor ID is empty, or if the query fails.
 *
 * @param int $donor_id The unique ID of the donor to search for.
 * @return GenDonor|null A GenDonor object if a donor is found, or null if no matching record exists.
 */
function find_user_detail_with_donor_id($donor_id)
{
    require_once "../../config/db.php";
    require_once "../../model/genDonor.php";

    if (empty($donor_id)) {
        return null;
    }

    $conn = prepare_new_connection();

    $stmt = mysqli_prepare($conn, "SELECT donor_id, full_name, password_hash, unique_id,
    email_id, dob, fathers_name, mothers_name, address, phone_number, state_code, pincode, gender, blood_group, disease, notes
                                   FROM gen_donors
                                   WHERE donor_id = ?
                                   LIMIT 1");
    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "i", $donor_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $records = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if (!$records) {
        // no user found
        return null;
    }

    // build object only when user exists
    $gen_donor = new GenDonor(
        donor_id: $records['donor_id'],
        full_name: $records['full_name'],
        password_hash: $records['password_hash'],
        unique_id: $records['unique_id'],
        email_id: $records['email_id'],
        dob: $records['dob'],
        fathers_name: $records['fathers_name'],
        mothers_name: $records['mothers_name'],
        address: $records['address'],
        phone_number: $records['phone_number'],
        state_code: $records['state_code'],
        pincode: $records['pincode'],
        gender: $records['gender'],
        blood_group: $records['blood_group'],
        disease: $records['disease'],
        notes: $records['notes'],
    );

    return $gen_donor;

}

/**
 * Retrieves a donor's information by phone number.
 *
 * This function searches the `gen_donors` table for a record matching
 * the specified 10-digit phone number. If a matching donor is found,
 * it returns a GenDonor object containing all relevant donor details.
 * Returns null if no donor is found, the phone number is invalid, or if the query fails.
 *
 * @param string $phone_number The 10-digit phone number of the donor to search for.
 * @return GenDonor|null A GenDonor object if a donor is found, or null if no matching record exists.
 */
function find_user_detail_with_phone($phone_number)
{
    require_once "../../config/db.php";
    require_once "../../model/genDonor.php";

    if (trim($phone_number) == '' || sizeof(str_split($phone_number)) != 10 || !is_numeric($phone_number)) {
        return null;
    }

    $conn = prepare_new_connection();


    $stmt = mysqli_prepare($conn, "SELECT donor_id, full_name, password_hash, unique_id,
    email_id, dob, fathers_name, mothers_name, address, phone_number, state_code, pincode, gender, blood_group, disease, notes
                                   FROM gen_donors
                                   WHERE phone_number = ?
                                   LIMIT 1");
    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return null;
    }

    mysqli_stmt_bind_param($stmt, "s", $phone_number);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $records = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if (!$records) {
        // no user found
        return null;
    }

    // build object only when user exists
    $gen_donor = new GenDonor(
        donor_id: $records['donor_id'],
        full_name: $records['full_name'],
        password_hash: $records['password_hash'],
        unique_id: $records['unique_id'],
        email_id: $records['email_id'],
        dob: $records['dob'],
        fathers_name: $records['fathers_name'],
        mothers_name: $records['mothers_name'],
        address: $records['address'],
        phone_number: $records['phone_number'],
        state_code: $records['state_code'],
        pincode: $records['pincode'],
        gender: $records['gender'],
        blood_group: $records['blood_group'],
        disease: $records['disease'],
        notes: $records['notes'],
    );

    return $gen_donor;

}


/**
 * Retrieves all donors with a specific blood group in a given state.
 *
 * This function searches the `gen_donors` table for all donors who:
 * - Have the specified blood group.
 * - Are located in the specified state (state code).
 *
 * Each matching donor is returned as a GenDonor object. The function
 * returns an array of GenDonor objects. If no matching donors are found
 * or if the query fails, it returns an empty array or null.
 *
 * @param string $blood_group The blood group to filter donors by (e.g., 'A+', 'O-', etc.).
 * @param int $state_code The state code to filter donors by.
 * @return GenDonor[]|null An array of GenDonor objects matching the criteria, or null if the query fails.
 */
function find_user_with_bg_in_state($blood_group, $state_code)
{
    require_once "../../config/db.php";
    require_once "../../model/genDonor.php";

    $conn = prepare_new_connection();
    $records = [];

    $stmt = mysqli_prepare($conn, "SELECT donor_id, full_name, unique_id,
    email_id, dob, fathers_name, mothers_name, address, phone_number, state_code, pincode, gender, blood_group, disease, notes
                                   FROM gen_donors
                                   WHERE state_code = ? AND blood_group = ?");
    if (!$stmt) {
        mysqli_close($conn);
        return null;
    }


    mysqli_stmt_bind_param($stmt, "is", $state_code, $blood_group);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    while ($record = $result->fetch_assoc()) {
        // build object only when user exists
        $gen_donor = new GenDonor(
            donor_id: $record['donor_id'],
            full_name: $record['full_name'],
            password_hash: ' ',
            unique_id: $record['unique_id'],
            email_id: $record['email_id'],
            dob: $record['dob'],
            fathers_name: $record['fathers_name'],
            mothers_name: $record['mothers_name'],
            address: $record['address'],
            phone_number: $record['phone_number'],
            state_code: $record['state_code'],
            pincode: $record['pincode'],
            gender: $record['gender'],
            blood_group: $record['blood_group'],
            disease: $record['disease'],
            notes: $record['notes'],
        );

        $records[] = $gen_donor;
    }

    return $records;

}

/**
 * Checks if a donor exists based on email or phone number.
 *
 * This function queries the `gen_donors` table to determine if there is
 * any donor with the given email ID or phone number. It returns true if
 * a matching donor exists, or false otherwise.
 *
 * @param string $email_id The email address of the donor to check.
 * @param string $phone_number The phone number of the donor to check.
 * @return bool True if a donor with the specified email or phone exists, false otherwise.
 */
function does_user_exist($email_id, $phone_number)
{
    require_once __DIR__ . "../../config/db.php";
    // require_once $_SERVER['DOCUMENT_ROOT'] . "/src/config/db.php";

    $conn = prepare_new_connection();

    $stmt = mysqli_prepare($conn, "SELECT full_name
                                   FROM gen_donors
                                   WHERE email_id = ?
                                    OR phone_number = ?
                                   LIMIT 1");
    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "ss", $email_id, $phone_number);
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
 * Registers a new donor in the database.
 *
 * This function inserts a new donor record into the `gen_donors` table
 * using the information provided in a GenDonor object. It includes personal
 * details, contact information, blood group, health details, unique ID,
 * password hash, and date of birth.
 *
 * @param GenDonor $gen_donor The GenDonor object containing the donor's information.
 * @return bool True if the donor was successfully registered, false if the insertion fails or the database connection fails.
 */
function register_user(
    GenDonor $gen_donor
) {
    require_once __DIR__ . "../../config/db.php";
    require_once __DIR__ . "../../model/genDonor.php";

    $conn = prepare_new_connection();

    $fullname = $gen_donor->getFullName();
    $email = $gen_donor->getEmailId();
    $fathers_name = $gen_donor->getFathersName();
    $mothers_name = $gen_donor->getMothersName();
    $address = $gen_donor->getAddress();
    $phone_number = $gen_donor->getPhoneNumber();
    $state_id = $gen_donor->getStateCode();
    $pincode = $gen_donor->getPincode();
    $gender = $gen_donor->getGender();
    $blood_group = $gen_donor->getBloodGroup();
    $disease = $gen_donor->getDisease();
    $notes = $gen_donor->getNotes();
    $unique_id = $gen_donor->getUniqueId();
    $password_hash = $gen_donor->getPasswordHash();
    $dob = $gen_donor->getDOB();


    $sql_q = "INSERT INTO gen_donors(
                        full_name, email_id, fathers_name, mothers_name, address,
                        phone_number, state_code, pincode, gender, blood_group,
                        disease, notes, unique_id, password_hash, dob
                        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = mysqli_prepare($conn, $sql_q);

    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssssiisssssss",
        $fullname,
        $email,
        $fathers_name,
        $mothers_name,
        $address,
        $phone_number,
        $state_id,
        $pincode,
        $gender,
        $blood_group,
        $disease,
        $notes,
        $unique_id,
        $password_hash,
        $dob
    );

    $exr = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $exr;

}

/**
 * Updates an existing donor's information in the database.
 *
 * This function updates the `gen_donors` table with the details provided
 * in a GenDonor object for the donor identified by donor_id. It allows
 * modification of personal information, contact details, blood group,
 * health information, notes, and date of birth.
 *
 * @param GenDonor $gen_donor The GenDonor object containing updated donor information.
 * @return bool True if the update was successful, false if the query fails or the database connection fails.
 */
function update_user(
    GenDonor $gen_donor
) {
    require_once __DIR__ . "../../config/db.php";
    require_once __DIR__ . "../../model/genDonor.php";

    $conn = prepare_new_connection();

    $donor_id = $gen_donor->getDonorId();
    $fullname = $gen_donor->getFullName();
    $fathers_name = $gen_donor->getFathersName();
    $mothers_name = $gen_donor->getMothersName();
    $address = $gen_donor->getAddress();
    $state_id = $gen_donor->getStateCode();
    $pincode = $gen_donor->getPincode();
    $gender = $gen_donor->getGender();
    $blood_group = $gen_donor->getBloodGroup();
    $disease = $gen_donor->getDisease();
    $notes = $gen_donor->getNotes();
    $dob = $gen_donor->getDOB();

    $sql_q = "UPDATE gen_donors
              SET full_name = ?, fathers_name = ?, mothers_name = ?, address = ?,
              state_code = ?, pincode = ?, gender = ?, blood_group = ?,
              disease = ?, notes = ?, dob = ?
              WHERE donor_id = ?";

    $stmt = mysqli_prepare($conn, $sql_q);

    if (!$stmt) {
        // query preparation failed
        mysqli_close($conn);
        return false;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssssiisssssi",
        $fullname,
        $fathers_name,
        $mothers_name,
        $address,
        $state_id,
        $pincode,
        $gender,
        $blood_group,
        $disease,
        $notes,
        $dob,
        $donor_id
    );

    $exr = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $exr;

}

/**
 * Creates a donor-specific records table if it does not already exist.
 *
 * This function generates a unique table for each donor to store their
 * blood donation and request history. The table name is constructed
 * using the donor's ID and phone number. The table includes fields for
 * record number, record type (donation, request, fulfilled), blood group,
 * notes, bank ID, and the date of the record.
 *
 * @param int $donor_id The unique ID of the donor.
 * @param string $phone_number The phone number of the donor.
 * @return bool True if the table already exists or was successfully created, false if creation fails.
 */
function create_donor_relatedtables($donor_id, $phone_number)
{
    require_once __DIR__ . "../../config/db.php";

    $conn = prepare_new_connection();
    if (!$conn) {
        return false;
    }

    $table_name = '' . $donor_id . $phone_number . '_records';

    // Create Donor Records table if not exists ................

    $check_stmt_for_tables = $conn->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?");
    $check_stmt_for_tables->bind_param("s", $table_name);
    $check_stmt_for_tables->execute();
    $check_result = $check_stmt_for_tables->get_result();

    if ($check_result->num_rows === 0) {
        //needs to create table  ...
        $records_tb = $conn->prepare("CREATE TABLE " . $table_name . "(record_number INT(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
    record_type ENUM('donation', 'request' , 'fulfilled') NOT NULL,
    blood_group ENUM('a' , 'ap' , 'b' , 'bp' , 'ab' ,  'abp' , 'o', 'op') NOT NULL, note TEXT NOT NULL, bank_id INT(11) NOT NULL,
    date DATE NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (bank_id) REFERENCES blood_banks(bank_id))");

        $result = $records_tb->execute();
        return $result;
    }


    $check_stmt_for_tables->close();
    $conn->close();
    return true;
}



?>

