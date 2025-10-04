<?php

// Database connection parameters
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root1');
define('DB_PASSWORD', 'root1');
define('DB_NAME', 'bbms');

function prepare_new_connection()
{
    // Attempt to connect to MySQL database
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Set character set to UTF-8
    $conn->set_charset("utf8");
    return $conn;
}

?>

