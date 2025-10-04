<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/controller/admin_brequest_controller.php";
require_once __DIR__ . "/../src/model/bankIdBloodRequest.php";
require_once __DIR__ . "/../src/config/db.php";

class BloodRequestTest extends TestCase
{
    private $conn;
    private $requests_table = "17777777777_blood_request";

    protected function setUp(): void
    {
        $this->conn = prepare_new_connection();
        $this->assertNotNull($this->conn, "Database connection failed in setup.");
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testAddBloodRequestValid()
    {
        $req = new BankIdBloodRequest(
            request_id: 1,
            requested_by: "Test User",
            requested_by_id: 101,
            requested_blood_group: "a",
            requested_for: 202,
            bank_id: 1,
            requested_on: date("Y-m-d"),
            status: "requested",
            note: "Urgent need"
        );

        $result = add_blood_request($this->requests_table, $req);
        $this->assertTrue($result, "Failed to insert valid blood request.");
    }

    public function testAddBloodRequestInvalidBloodGroup()
    {
        $req = new BankIdBloodRequest(
            request_id: 1,
            requested_by: "Invalid User",
            requested_by_id: 102,
            requested_blood_group: "xyz", // invalid
            requested_for: 203,
            bank_id: 1,
            requested_on: date("Y-m-d"),
            status: "requested",
            note: "Invalid blood group test"
        );

        $result = add_blood_request($this->requests_table, $req);
        $this->assertFalse($result, "Inserted blood request with invalid blood group.");
    }

    public function testUpdateRequestStatusValid()
    {
        // Insert a dummy request first
        $this->conn->query("
            INSERT INTO {$this->requests_table}
            (requested_by, requested_by_id, requested_blood_group, requested_for, bank_id, requested_on, note, status)
            VALUES ('Updater', 103, 'b', 204, 1, CURDATE(), 'Initial', 'requested')
        ");
        $request_id = $this->conn->insert_id;

        $result = update_request_status($this->requests_table, $request_id, "fulfilled", 1);
        $this->assertTrue($result, "Failed to update request status to fulfilled.");

        // Verify
        $res = $this->conn->query("SELECT status FROM {$this->requests_table} WHERE request_id = {$request_id}");
        $row = $res->fetch_assoc();
        $this->assertEquals("fulfilled", $row['status']);
    }

    public function testUpdateRequestStatusInvalidId()
    {
        $result = update_request_status($this->requests_table, 99999, "discarded", 1);
        $this->assertFalse($result, "Updated a non-existent request ID.");
    }

    // public function testSearchFromRequestsAll()
    // {
    //     require_once __DIR__ . "/../src/config/filter_searchBrequests.php";
    //     global $filter_requests;

    //     // Insert dummy requests
    //     $this->conn->query("
    //         INSERT INTO {$this->requests_table}
    //         (requested_by, requested_by_id, requested_blood_group, requested_for, bank_id, requested_on, note, status)
    //         VALUES ('Searcher', 105, 'o', 206, 1, CURDATE(), 'Test Search', 'requested')
    //     ");

    //     $records = search_from_requests($this->requests_table, "all", "Searcher");
    //     $this->assertNotEmpty($records, "Search returned no records when it should.");
    //     $this->assertInstanceOf(BankIdBloodRequest::class, $records[0]);
    // }


    // public function testSearchFromRequestsInvalidFilter()
    // {
    //     // Insert dummy request
    //     $this->conn->query("
    //         INSERT INTO {$this->requests_table}
    //         (requested_by, requested_by_id, requested_blood_group, requested_for, bank_id, requested_on, note, status)
    //         VALUES ('NoFilter', 106, 'op', 207, 1, CURDATE(), 'Invalid filter test', 'requested')
    //     ");

    //     $records = search_from_requests($this->requests_table, "invalid_filter", "NoFilter");
    //     $this->assertNotEmpty($records, "Search with invalid filter should return all records.");
    // }
}
