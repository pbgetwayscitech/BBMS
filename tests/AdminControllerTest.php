<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . "/../src/controller/admin_controller.php";
require_once __DIR__ . "/../src/model/bloodBank.php";
require_once __DIR__ . "/../src/config/db.php";

class AdminControllerTest extends TestCase
{
    private $conn;

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

    public function testRegisterBloodBank()
    {
        $bank = new BloodBank(
            bank_id: 9,
            bank_name: "Test Blood Bank",
            pincode: 123456,
            state_id: 1,
            bank_owner: "Owner Name",
            address: "123 Test St",
            phone_number: "9876543210",
            bank_email: "testbank@example.com",
            password_hash: password_hash("password", PASSWORD_DEFAULT),
            a_stock: 0,
            ap_stock: 0,
            b_stock: 0,
            bp_stock: 0,
            o_stock: 0,
            op_stock: 0,
            ab_stock: 0,
            abp_stock: 0
        );

        $result = register_blood_bank($bank);
        $this->assertTrue($result, "Failed to register new blood bank.");
    }

    public function testSearchAdminWithEmailValid()
    {
        $admin = search_admin_with_email("testbank@example.com");
        $this->assertInstanceOf(BloodBank::class, $admin);
        $this->assertEquals("Test Blood Bank", $admin->getBankName());
    }

    public function testSearchAdminWithEmailInvalid()
    {
        $admin = search_admin_with_email("nonexistent@example.com");
        $this->assertNull($admin, "Non-existent email should return null.");
    }

    public function testDoesAdminExistTrue()
    {
        $exists = does_admin_exist("Test Blood Bank", "testbank@example.com", "9876543210");
        $this->assertTrue($exists, "Admin should exist.");
    }

    public function testDoesAdminExistFalse()
    {
        $exists = does_admin_exist("Fake Bank", "fake@example.com", "0000000000");
        $this->assertFalse($exists, "Admin should not exist.");
    }

    public function testGetBloodGroupQtyForBankEmail()
    {
        $admin = search_admin_with_email("testbank@example.com");
        $qty = get_blood_group_qty_for_bank_email("testbank@example.com", $admin->getBankId(), "a");
        $this->assertEquals(0, $qty);
    }

    public function testCreateOtherBankRelatedTables()
    {
        $admin = search_admin_with_email("testbank@example.com");
        $result = create_other_bank_related_tables($admin->getBankId(), $admin->getPhoneNumber());
        $this->assertTrue($result, "Failed to create bank-related tables.");
    }

    public function testUpdateBloodStockParamValid()
    {
        $admin = search_admin_with_email("testbank@example.com");
        $result = update_blood_stock_param($admin->getBankId(), $admin->getPhoneNumber(), "a", 5);
        $this->assertTrue($result, "Failed to update blood stock parameter.");

        $qty = get_blood_group_qty_for_bank_email($admin->getBankEmail(), $admin->getBankId(), "a");
        $this->assertEquals(5, $qty);
    }

    public function testUpdateBloodStockParamInvalid()
    {
        $result = update_blood_stock_param(null, "", "a", 5);
        $this->assertFalse($result, "Update should fail with invalid input.");
    }
}
