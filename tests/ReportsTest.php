<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/controller/reports_controller.php";
require_once __DIR__ . "/../src/config/db.php";

class ReportsTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = prepare_new_connection();
        $this->assertNotNull($this->conn, "Database connection failed.");

        // Insert sample blood banks
        $this->conn->query("INSERT INTO blood_banks (bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email, password_hash, a, ap, b, bp, o, op, ab, abp)
                            VALUES ('Bank1', 123456, 1, 'Owner1', 'Address1', '1111111111', 'bank1@test.com', 'passhash', 5, 0, 3, 0, 2, 1, 0, 0)");
        $this->conn->query("INSERT INTO blood_banks (bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email, password_hash, a, ap, b, bp, o, op, ab, abp)
                            VALUES ('Bank2', 654321, 2, 'Owner2', 'Address2', '2222222222', 'bank2@test.com', 'passhash', 0, 0, 0, 0, 0, 0, 0, 0)");

        // Insert sample donors
        $this->conn->query("INSERT INTO gen_donors (full_name, blood_group, state_code, gender, email_id, phone_number, password_hash , unique_id)
                            VALUES ('Donor1', 'A+', 1, 'Male', 'donor1@test.com', '9999999991', 'pass', '7418523')");
        $this->conn->query("INSERT INTO gen_donors (full_name, blood_group, state_code, gender, email_id, phone_number, password_hash, unique_id)
                            VALUES ('Donor2', 'B+', 2, 'Female', 'donor2@test.com', '9999999992', 'pass', '65428')");
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testGetBanksCountWithStateId()
    {
        $count = get_banks_count_with_state_id(1);
        $this->assertEquals(1, $count);

        $count = get_banks_count_with_state_id(2);
        $this->assertEquals(1, $count);

        $count = get_banks_count_with_state_id(3);
        $this->assertEquals(0, $count);
    }

    public function testGetTotalBloodStockByBloodGroup()
    {
        $totalA = get_total_blood_stock_by_blood_group('a');
        $this->assertEquals(5, $totalA);

        $totalB = get_total_blood_stock_by_blood_group('b');
        $this->assertEquals(3, $totalB);

        $totalO = get_total_blood_stock_by_blood_group('o');
        $this->assertEquals(2, $totalO);

        $totalAP = get_total_blood_stock_by_blood_group('ap');
        $this->assertEquals(0, $totalAP);
    }

    public function testGetTotalBanks()
    {
        $totalBanks = get_total_banks();
        $this->assertEquals(2, $totalBanks);
    }

    public function testGetDonorCountByStateId()
    {
        $count1 = get_donor_count_by_state_id(1);
        $this->assertEquals(1, $count1);

        $count2 = get_donor_count_by_state_id(2);
        $this->assertEquals(1, $count2);

        $count3 = get_donor_count_by_state_id(3);
        $this->assertEquals(0, $count3);
    }

    public function testGetDonorCountWithGender()
    {
        $maleCount = get_donor_count_with_gender('Male');
        $this->assertEquals(1, $maleCount);

        $femaleCount = get_donor_count_with_gender('Female');
        $this->assertEquals(1, $femaleCount);

        $otherCount = get_donor_count_with_gender('Other');
        $this->assertEquals(0, $otherCount);
    }

    public function testGetDonorCountWithBloodGroup()
    {
        $countA = get_donor_count_with_blood_group('A+');
        $this->assertEquals(1, $countA);

        $countB = get_donor_count_with_blood_group('B+');
        $this->assertEquals(1, $countB);

        $countO = get_donor_count_with_blood_group('O+');
        $this->assertEquals(0, $countO);
    }

    public function testGetTotalDonorCount()
    {
        $total = get_total_donor_count();
        $this->assertEquals(2, $total);
    }
}
