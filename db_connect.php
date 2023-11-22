<?php
// db_connect.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "yasaynew";

// Create connection
$connect = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connect->connect_errno) {
    die("Database connection failed: " . $connect->connect_error);
}
?>