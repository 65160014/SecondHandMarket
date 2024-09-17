<?php
include 'db_connect.php';

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "การเชื่อมต่อล้มเหลว: " . $conn->connect_error]));
}

$response = ["success" => false, "message" => ""];

// ตรวจสอบว่าฟอร์มถูกส่งหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $Phone = $_POST['Phone'];
    $Province = $_POST['Province'];
    $District = $_POST['District'];
    $ZipCode = $_POST['ZipCode'];
    $FurtherDescription = $_POST['FurtherDescription'];

    // ตรวจสอบค่าที่ได้รับจากฟอร์ม
    if (empty($name) || empty($Phone) || empty($Province) || empty($District) || empty($ZipCode)) {
        $response['message'] = "กรุณากรอกข้อมูลทุกช่อง";
    } else {
        // เตรียมและผูกข้อมูล
        $stmt = $conn->prepare("INSERT INTO deliveryaddress (Province, District, ZipCode, FurtherDescription, name, Phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $Province, $District, $ZipCode, $FurtherDescription, $name, $Phone);

        // รันคำสั่ง SQL
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "บันทึกข้อมูลสำเร็จ!";
        } else {
            $response['message'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
        }

        // ปิดการเชื่อมต่อฐานข้อมูล
        $stmt->close();
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ส่งผลลัพธ์กลับไปยัง JavaScript
header('Content-Type: application/json');
echo json_encode($response);
?>
