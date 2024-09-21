<?php
include 'db_connect.php';

// รับข้อมูลจาก POST
$orderID = $_POST['OrderID'];
$productID = $_POST['ProductID']; // รับ ProductID ที่ส่งมาจาก JavaScript
$shippingStatus = $_POST['ShippingStatus'];
$shippingName = $_POST['ShippingName'];

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// ตรวจสอบข้อมูลก่อนทำการ insert
if (empty($orderID) || empty($productID) || empty($shippingStatus) || empty($shippingName)) {
    echo json_encode(['success' => false, 'error' => 'Missing data']);
    exit;
}

// Log ข้อมูลที่จะแทรกลงใน orderproduct
error_log("Inserting into orderproduct: OrderID = $orderID, ProductID = $productID, ShippingStatus = $shippingStatus, ShippingName = $shippingName");

// ทำการ insert ข้อมูลในตาราง orderproduct
$insertStmt = $conn->prepare("INSERT INTO `orderproduct` (OrderID, ProductID, ShippingStatus, ShippingName) VALUES (?, ?, ?, ?)");
$insertStmt->bind_param("siss", $orderID, $productID, $shippingStatus, $shippingName);

if ($insertStmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $insertStmt->error]);
}

$insertStmt->close();
$conn->close();
?>
