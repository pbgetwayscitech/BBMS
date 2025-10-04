<?php

class GenDonor
{
    // Attributes based on the class diagram
    private ?int $donor_id;
    private string $full_name;
    private string $email_id;
    private string $fathers_name;
    private string $mothers_name;
    private string $dob;
    private string $address;
    private int $phone_number; // Assuming BIGINT(10) maps to int in PHP for common phone numbers
    private int $state_code;   // State ID
    private int $pincode;
    private string $gender;    // ENUM: 'male', 'female'
    private string $blood_group; // ENUM: 'a', 'ap', 'b', 'bp', 'ab', 'abp', 'o', 'op'
    private string $disease;
    private string $notes;
    private string $unique_id;     // Unique donor identifier
    private string $password_hash; // Hashed password or combined hash for security

    /**
     * Constructor for the GenDonor class.
     *
     * @param int $donor_id Unique identifier for the donor.
     * @param string $full_name Full name of the donor.
     * @param string $email_id Email address of the donor.
     * @param string $fathers_name Father's name of the donor.
     * @param string $mothers_name Mother's name of the donor.
     * @param string $dob //Date of Birth
     * @param string $address Address of the donor.
     * @param int $phone_number Phone number of the donor.
     * @param int $state_code State code of the donor.
     * @param int $pincode Pincode of the donor's address.
     * @param string $gender Gender of the donor ('male' or 'female').
     * @param string $blood_group Blood group of the donor.
     * @param string $disease Any diseases the donor has (use "NA" if none).
     * @param string $notes Additional notes about the donor (use "NA" if none).
     * @param string $unique_id Unique ID assigned to the donor.
     * @param string $password_hash Combined hash of email and password for authentication.
     */
    public function __construct(
        ?int $donor_id,
        string $full_name,
        string $email_id,
        string $fathers_name,
        string $mothers_name,
        string $dob,
        string $address,
        int $phone_number,
        int $state_code,
        int $pincode,
        string $gender,
        string $blood_group,
        string $disease,
        string $notes,
        string $unique_id,
        string $password_hash
    ) {
        $this->donor_id = $donor_id;
        $this->full_name = $full_name;
        $this->email_id = $email_id;
        $this->fathers_name = $fathers_name;
        $this->mothers_name = $mothers_name;
        $this->dob = $dob;
        $this->address = $address;
        $this->phone_number = $phone_number;
        $this->state_code = $state_code;
        $this->pincode = $pincode;
        $this->gender = $gender;
        $this->blood_group = $blood_group;
        $this->disease = $disease;
        $this->notes = $notes;
        $this->unique_id = $unique_id;
        $this->password_hash = $password_hash;
    }

    // --- Getters ---
    public function getDonorId(): int
    {
        return $this->donor_id;
    }

    public function getFullName(): string
    {
        return $this->full_name;
    }

    public function getEmailId(): string
    {
        return $this->email_id;
    }

    public function getFathersName(): string
    {
        return $this->fathers_name;
    }

    public function getMothersName(): string
    {
        return $this->mothers_name;
    }

    public function getDOB(): string
    {
        return $this->dob;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhoneNumber(): int
    {
        return $this->phone_number;
    }

    public function getStateCode(): int
    {
        return $this->state_code;
    }

    public function getPincode(): int
    {
        return $this->pincode;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getBloodGroup(): string
    {
        return $this->blood_group;
    }

    public function getDisease(): string
    {
        return $this->disease;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getUniqueId(): string
    {
        return $this->unique_id;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    // --- Setters (for potential updates to donor profile) ---
    public function setFullName(string $full_name): void
    {
        $this->full_name = $full_name;
    }

    public function setEmailId(string $email_id): void
    {
        $this->email_id = $email_id;
    }

    public function setFathersName(string $fathers_name): void
    {
        $this->fathers_name = $fathers_name;
    }

    public function setMothersName(string $mothers_name): void
    {
        $this->mothers_name = $mothers_name;
    }

    public function setDOB(string $dob): void
    {
        $this->dob = $dob;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setPhoneNumber(int $phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    public function setStateCode(int $state_code): void
    {
        $this->state_code = $state_code;
    }

    public function setPincode(int $pincode): void
    {
        $this->pincode = $pincode;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function setBloodGroup(string $blood_group): void
    {
        $this->blood_group = $blood_group;
    }

    public function setDisease(string $disease): void
    {
        $this->disease = $disease;
    }

    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    public function setUniqueId(string $unique_id): void
    {
        $this->unique_id = $unique_id;
    }

    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }
}


?>

