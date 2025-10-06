<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/controller/search_controller.php';
require_once __DIR__ . '/../src/config/db.php';
require_once __DIR__ . '/../src/model/bloodBank.php';
require_once __DIR__ . '/../src/model/bankIdBloodRequest.php';
require_once __DIR__ . '/../src/model/UniqueIDRecords.php';

class SearchTest extends TestCase
{
    private $conn;
    private $bloodStockTable;
    private $bloodRequestTable;
    private $userRecordTable;

    protected function setUp(): void
    {
        $this->conn = prepare_new_connection();
        $this->assertNotNull($this->conn, "Database connection failed.");

        $this->bloodStockTable = "test_blood_stock";
        $this->bloodRequestTable = "test_blood_request";
        $this->userRecordTable = "test_user_records";

        $this->conn->query("CREATE TABLE IF NOT EXISTS blood_banks (
            bank_id INT AUTO_INCREMENT PRIMARY KEY,
            bank_name VARCHAR(255),
            pincode INT,
            state_id INT,
            bank_owner VARCHAR(255),
            address VARCHAR(255),
            phone_number VARCHAR(20),
            bank_email VARCHAR(255),
            password_hash VARCHAR(255),
            a INT DEFAULT 0, ap INT DEFAULT 0, b INT DEFAULT 0, bp INT DEFAULT 0, ab INT DEFAULT 0, abp INT DEFAULT 0, o INT DEFAULT 0, op INT DEFAULT 0
        )");

        $this->conn->query("INSERT INTO blood_banks (bank_name, pincode, state_id, bank_owner, address, phone_number, bank_email, password_hash, a, b, o)
                            VALUES ('Bank1', 123456, 1, 'Owner1', 'Address1', '1111111111', 'bank1@test.com', 'pass', 5, 3, 2),
                                   ('Bank2', 654321, 1, 'Owner2', 'Address2', '2222222222', 'bank2@test.com', 'pass', 0, 0, 1)");

        $this->conn->query("CREATE TABLE IF NOT EXISTS {$this->bloodRequestTable} (
            request_id INT AUTO_INCREMENT PRIMARY KEY,
            requested_by TEXT,
            requested_by_id INT,
            requested_blood_group ENUM('a','ap','b','bp','ab','abp','o','op'),
            requested_for INT,
            bank_id INT,
            requested_on DATE,
            status ENUM('requested','fulfilled','discarded'),
            note TEXT
        )");

        $this->conn->query("CREATE TABLE IF NOT EXISTS {$this->userRecordTable} (
            record_id INT AUTO_INCREMENT PRIMARY KEY,
            record_number VARCHAR(255),
            record_type VARCHAR(255),
            blood_group VARCHAR(10),
            note TEXT,
            bank_id INT,
            date DATE
        )");
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM blood_banks WHERE bank_id in (SELECT bank_id FROM (SELECT bank_id FROM blood_banks) AS temp)");
        $this->conn->close();
    }

    public function testSearchBankWithCriteria()
    {
        global $blood_groups, $states;
        $blood_groups = ['a' => 'A+', 'b' => 'B+', 'o' => 'O+'];
        $states = [1 => 'State1', 2 => 'State2'];

        $results = search_bank_with_criteria("", 'a', 1);
        $this->assertCount(1, $results);
        $this->assertEquals('Bank1', $results[0]->getBankName());

        $results = search_bank_with_criteria("Bank2", 'o', 1);
        $this->assertCount(1, $results);
        $this->assertEquals('Bank2', $results[0]->getBankName());

        $results = search_bank_with_criteria("", 'ap', 5);
        $this->assertNull($results);
    }

    public function testAddBloodRequest()
    {
        $request = new BankIdBloodRequest(
            request_id: 1,
            requested_by: 'Admin1',
            requested_by_id: 101,
            requested_blood_group: 'a',
            requested_for: 201,
            bank_id: 1,
            requested_on: date('Y-m-d'),
            status: 'requested',
            note: 'Urgent'
        );

        $result = add_blood_request($this->bloodRequestTable, $request);
        $this->assertTrue($result);

        $res = $this->conn->query("SELECT * FROM {$this->bloodRequestTable} WHERE requested_by='Admin1'");
        $this->assertEquals(1, $res->num_rows);
    }

    public function testAddDataToUserRecord()
    {
        $record = new UniqueIdRecord(
            record_number: '1',
            record_type: 'Donation',
            blood_group: 'ap',
            note: 'Test note',
            bank_id: 1,
            date: date('Y-m-d')
        );

        $result = add_data_to_user_record($this->userRecordTable, $record);
        $this->assertTrue($result);

        $res = $this->conn->query("SELECT * FROM {$this->userRecordTable} WHERE record_number='1'");
        $this->assertEquals(1, $res->num_rows);
    }
}
