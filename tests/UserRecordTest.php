<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/controller/user_record_controller.php'; // adjust path
require_once __DIR__ . '../../src/config/db.php';
require_once __DIR__ . '../../src/model/UniqueIDRecords.php';
require_once __DIR__ . '../../src/config/filter_records.php';

class UserRecordTest extends TestCase
{
    private $conn;
    private $userRecordTable;

    protected function setUp(): void
    {
        $this->conn = prepare_new_connection();
        $this->assertNotNull($this->conn, "Database connection failed.");

        $this->userRecordTable = "test_user_records";
        $this->conn->query("CREATE TABLE IF NOT EXISTS {$this->userRecordTable} (
            record_number INT(11) PRIMARY KEY AUTO_INCREMENT,
            record_type VARCHAR(255),
            blood_group enum('a', 'ap', 'b', 'bp', 'ab', 'abp', 'o', 'op'),
            note TEXT,
            bank_id INT(11),
            date DATE
        )");

        global $filter_records;
        $filter_records = [
            'record_number' => 'Record Number',
            'record_type' => 'Record Type',
            'blood_group' => 'Blood Group',
            'note' => 'Note',
            'bank_id' => 'Bank ID'
        ];

        $this->conn->query("INSERT INTO {$this->userRecordTable} (record_number, record_type, blood_group, note, bank_id, date)
                            VALUES ('1', 'Donation', 'ap', 'First donation', 1, CURDATE()),
                                   ('2', 'Request', 'bp', 'Blood needed', 2, CURDATE())");
    }

    protected function tearDown(): void
    {
        $this->conn->query("DELETE FROM test_user_records");
        $this->conn->close();
    }

    public function testFindRecordsWithFilterAll()
    {
        $results = find_records($this->userRecordTable, 'Donation', 'all');
        $this->assertCount(1, $results);
        $this->assertEquals('1', $results[0]->getRecordNumber());
    }

    public function testFindRecordsWithSpecificFilter()
    {
        $results = find_records($this->userRecordTable, 'bp', 'blood_group');
        $this->assertCount(1, $results);
        $this->assertEquals('2', $results[0]->getRecordNumber());
    }

    public function testFindRecordsWithEmptyQuery()
    {
        $results = find_records($this->userRecordTable, '', 'all');
        $this->assertCount(2, $results);
    }

    public function testFindRecordsWithInvalidFilter()
    {
        $results = find_records($this->userRecordTable, 'Donation', 'invalid_column');
        $this->assertCount(0, $results);
    }

    public function testAddDataToUserRecord()
    {
        $record = new UniqueIdRecord(
            record_number: null,
            record_type: 'Donation',
            blood_group: 'op',
            note: 'Test Fetcher CODE X123',
            bank_id: 1,
            date: date('Y-m-d')
        );

        $result = add_data_to_user_record($this->userRecordTable, $record);
        $this->assertTrue($result);

        // Verify insertion in DB
        $res = $this->conn->query("SELECT * FROM {$this->userRecordTable} WHERE note = 'Test Fetcher CODE X123' ");
        $this->assertEquals(1, $res->num_rows);
    }
}
