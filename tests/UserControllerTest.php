<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/controller/user_controller.php";
require_once __DIR__ . "/../src/model/genDonor.php";

class UserControllerTest extends TestCase
{
    private $testEmail = "unit_test_donor@example.com";
    private $testPhone = "9876543210";
    private $testUniqueId = "UT123456";
    private $donorId;

    /**
     * Prepare a test donor before running tests
     */
    protected function setUp(): void
    {
        // Ensure no duplicate before inserting
        if (does_user_exist($this->testEmail, $this->testPhone)) {
            return;
        }

        $donor = new GenDonor(
            donor_id: 0,
            full_name: "Unit Test Donor",
            password_hash: password_hash("password123", PASSWORD_BCRYPT),
            unique_id: $this->testUniqueId,
            email_id: $this->testEmail,
            dob: "1990-01-01",
            fathers_name: "Father Test",
            mothers_name: "Mother Test",
            address: "123 Test Street",
            phone_number: $this->testPhone,
            state_code: 1,
            pincode: 123456,
            gender: "male",
            blood_group: "a",
            disease: "none",
            notes: "unit testing"
        );

        $result = register_user($donor);
        $this->assertTrue($result, "Failed to register test donor");

        // fetch inserted donor_id
        $obj = search_user_with_email($this->testEmail);
        $this->donorId = $obj ? $obj->getDonorId() : null;
    }

    public function testSearchUserWithEmailValid()
    {
        $donor = search_user_with_email($this->testEmail);
        $this->assertNotNull($donor);
        $this->assertEquals($this->testEmail, $donor->getEmailId());
    }

    public function testSearchUserWithEmailInvalid()
    {
        $donor = search_user_with_email("notfound@example.com");
        $this->assertNull($donor);
    }

    public function testFindUserDetailWithDonorIdValid()
    {
        $donor = find_user_detail_with_donor_id($this->donorId);
        $this->assertNotNull($donor);
        $this->assertEquals($this->donorId, $donor->getDonorId());
    }

    public function testFindUserDetailWithDonorIdInvalid()
    {
        $donor = find_user_detail_with_donor_id(999999);
        $this->assertNull($donor);
    }

    public function testFindUserDetailWithPhoneValid()
    {
        $donor = find_user_detail_with_phone($this->testPhone);
        $this->assertNotNull($donor);
        $this->assertEquals($this->testPhone, $donor->getPhoneNumber());
    }

    public function testFindUserDetailWithPhoneInvalid()
    {
        $donor = find_user_detail_with_phone("12345");
        $this->assertNull($donor);
    }

    public function testFindUserWithBGInState()
    {
        $records = find_user_with_bg_in_state("a", 1);
        $this->assertIsArray($records);
        $this->assertNotEmpty($records);
        $this->assertEquals("a", $records[0]->getBloodGroup());
    }

    public function testDoesUserExist()
    {
        $exists = does_user_exist($this->testEmail, $this->testPhone);
        $this->assertTrue($exists);
    }

    public function testRegisterDuplicateUser()
    {
        $donor = new GenDonor(
            donor_id: 0,
            full_name: "Duplicate Donor",
            password_hash: password_hash("password123", PASSWORD_BCRYPT),
            unique_id: $this->testUniqueId, // same unique_id should fail
            email_id: $this->testEmail, // same email should fail
            dob: "1990-01-01",
            fathers_name: "Father",
            mothers_name: "Mother",
            address: "456 Test Road",
            phone_number: $this->testPhone, // duplicate phone
            state_code: 1,
            pincode: 123456,
            gender: "male",
            blood_group: "a",
            disease: "none",
            notes: "duplicate testing"
        );

        $result = register_user($donor);
        $this->assertFalse($result, "Duplicate user registration should fail");
    }

    public function testUpdateUserValid()
    {
        $donor = find_user_detail_with_donor_id($this->donorId);
        $donor->setAddress("Updated Test Address");
        $result = update_user($donor);
        $this->assertTrue($result);

        $updated = find_user_detail_with_donor_id($this->donorId);
        $this->assertEquals("Updated Test Address", $updated->getAddress());
    }

    public function testCreateDonorRelatedTables()
    {
        $result = create_donor_relatedtables($this->donorId, $this->testPhone);
        $this->assertTrue($result, "Donor related table creation failed");
    }

    /**
     * Cleanup test data after running tests
     */
    protected function tearDown(): void
    {
        $conn = prepare_new_connection();
        $stmt = $conn->prepare("DELETE FROM gen_donors WHERE email_id = ?");
        $stmt->bind_param("s", $this->testEmail);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}
