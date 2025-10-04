<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/controller/admin_bstock_controller.php";
require_once __DIR__ . "/../src/model/bankIDbloodStock.php";
require_once __DIR__ . "/../src/config/db.php";
require_once __DIR__ . "/../src/config/filter_searchBstock.php";

class BloodStockTest extends TestCase
{
    private $conn;
    private $blood_stock_table = "17777777777_blood_stock";

    protected function setUp(): void
    {
        $this->conn = prepare_new_connection();
        $this->assertNotNull($this->conn, "Database connection failed.");
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function testAddNewDonationValid()
    {
        $stock = new BankIdBloodStock(
            stock_id: null,
            donor_id: 1,
            bank_id: 1,
            donation_date: date("Y-m-d"),
            blood_group: "a",
            expiary_date: date("Y-m-d", strtotime("+30 days")),
            note: "Unit test donation",
            stock_status: "preserved",
            stock_status_date: date("Y-m-d")
        );

        $result = add_new_donation($this->blood_stock_table, $stock);
        $this->assertTrue($result, "Failed to insert valid donation record.");
    }

    public function testAddNewDonationInvalidBloodGroup()
    {
        $stock = new BankIdBloodStock(
            stock_id: null,
            donor_id: 1,
            bank_id: 1,
            donation_date: date("Y-m-d"),
            blood_group: "xyz", // invalid
            expiary_date: date("Y-m-d", strtotime("+30 days")),
            note: "Invalid blood group",
            stock_status: "preserved",
            stock_status_date: date("Y-m-d")
        );

        $result = add_new_donation($this->blood_stock_table, $stock);
        $this->assertFalse($result, "Inserted a donation with invalid blood group.");
    }

    public function testUpdateBloodStockStatusValid()
    {
        // Insert dummy stock
        $this->conn->query("
            INSERT INTO {$this->blood_stock_table}
            (donor_id, blood_group, bank_id, donation_date, expiary_date, note, stock_status, stock_status_date)
            VALUES (1, 'b', 1, CURDATE(), CURDATE() + INTERVAL 30 DAY, 'Test update', 'preserved', CURDATE())
        ");
        $stock_id = $this->conn->insert_id;

        $result = update_blood_Stock_status($this->blood_stock_table, $stock_id, "utilised");
        $this->assertTrue($result, "Failed to update stock status to utilised.");

        $res = $this->conn->query("SELECT stock_status FROM {$this->blood_stock_table} WHERE stock_id = {$stock_id}");
        $row = $res->fetch_assoc();
        $this->assertEquals("utilised", $row['stock_status']);
    }

    public function testUpdateBloodStockStatusInvalidId()
    {
        $result = update_blood_Stock_status($this->blood_stock_table, 99999, "discarded");
        $this->assertFalse($result, "Updated a non-existent stock ID.");
    }

    public function testUpdateBloodStockStatusInvalidStatus()
    {
        // Insert dummy stock
        $this->conn->query("
            INSERT INTO {$this->blood_stock_table}
            (donor_id, blood_group, bank_id, donation_date, expiary_date, note, stock_status, stock_status_date)
            VALUES (1, 'op', 1, CURDATE(), CURDATE() + INTERVAL 30 DAY, 'Invalid status', 'preserved', CURDATE())
        ");
        $stock_id = $this->conn->insert_id;

        $result = update_blood_Stock_status($this->blood_stock_table, $stock_id, "invalid_status");
        $this->assertFalse($result, "Updated stock with invalid status value.");
    }

    public function testSearchBloodStockAll()
    {
        // Insert dummy record
        $this->conn->query("
            INSERT INTO {$this->blood_stock_table}
            (donor_id, blood_group, bank_id, donation_date, expiary_date, note, stock_status, stock_status_date)
            VALUES (1, 'ap', 1, CURDATE(), CURDATE() + INTERVAL 30 DAY, 'Search all', 'preserved', CURDATE())
        ");

        $records = search_blood_stock($this->blood_stock_table, "Search all", "all");
        $this->assertNotEmpty($records, "Search with 'all' filter returned no records.");
        $this->assertInstanceOf(BankIdBloodStock::class, $records[0]);
    }

    public function testSearchBloodStockByBloodGroup()
    {
        // Insert dummy record
        $this->conn->query("
            INSERT INTO {$this->blood_stock_table}
            (donor_id, blood_group, bank_id, donation_date, expiary_date, note, stock_status, stock_status_date)
            VALUES (1, 'b', 1, CURDATE(), CURDATE() + INTERVAL 30 DAY, 'Search by group', 'preserved', CURDATE())
        ");

        global $filter_searchBstock;
        $filter_searchBstock = ['blood_group' => 'blood_group']; // ensure filter key exists

        $records = search_blood_stock($this->blood_stock_table, "b", "blood_group");
        $this->assertNotEmpty($records, "Search by blood group returned no records.");
        $this->assertEquals("b", $records[0]->getBloodGroup());
    }

    public function testSearchBloodStockInvalidFilter()
    {
        // Insert dummy record
        $this->conn->query("
            INSERT INTO {$this->blood_stock_table}
            (donor_id, blood_group, bank_id, donation_date, expiary_date, note, stock_status, stock_status_date)
            VALUES (1, 'o', 1, CURDATE(), CURDATE() + INTERVAL 30 DAY, 'Invalid filter', 'preserved', CURDATE())
        ");

        $records = search_blood_stock($this->blood_stock_table, "o", "invalid_filter");
        $this->assertNotEmpty($records, "Search with invalid filter should return all records.");
    }
}
