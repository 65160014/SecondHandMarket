<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "myschdb";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    // http://192.168.0.133/prototype/index.php
    // http://192.168.11.169/prototype/index.php (new)
}

?>

