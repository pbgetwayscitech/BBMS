<!-- This is the file for test file strucuture and command collection -->

// <?php

// echo " root .. " . $_SERVER['DOCUMENT_ROOT'] . "/src/config/db.php";

// ?>

<?php
use PHPUnit\Framework\TestCase;

// require_once __DIR__ . "/../../app/models/User.php";
// require_once __DIR__ . "/../../config/database.php";

// runnning test vendor/bin/phpunit --configuration tests/phpunit.xml
// vendor/bin/phpunit ./tests/UserTest.php

class UserTest extends TestCase
{

    private $db;

    protected function setUp(): void
    {
        //  $this->db = db_connect();
        $this->db = "wc";
    }

    public function testCheckTrue()
    {
        // Arrange: Insert a fake user for testing
        // Assert
        // $this->assertNotEmpty($db);

        $this->assertTrue(true, "Run Sucessfully");
    }

    public function testCheckfalse()
    {
        // Arrange: Insert a fake user for testing
        // Assert
        // $this->assertNotEmpty($db);

        $this->assertFalse(true, "Run Sucessfully");
    }

    protected function tearDown(): void
    {
        //  mysqli_query($this->db, "DELETE FROM gen_donors WHERE email_id = 'testuser@example.com'");
        $this->db = null;
    }
}
