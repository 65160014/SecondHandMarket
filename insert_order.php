<?php
    include 'db_connect.php';

    // เช็คการเชื่อมต่อฐานข้อมูล
    if (!$conn) {
        die(json_encode(['success' => false, 'error' => mysqli_connect_error()]));
    }

    // ตรวจสอบว่ามีการส่งข้อมูล POST มาครบถ้วนหรือไม่
    if (isset($_POST['OrderID'], $_POST['PaymentID'], $_POST['UserID'], $_POST['DeliveryAddressID'])) {
        $orderID = $_POST['OrderID'];
        $paymentID = $_POST['PaymentID'];
        $userID = $_POST['UserID'];
        $deliveryAddressID = $_POST['DeliveryAddressID'];

        // ใช้ Prepared Statements เพื่อความปลอดภัย
        $stmt = $conn->prepare("INSERT INTO `order` (OrderID, PaymentID, UserID, DeliveryAddressID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $orderID, $paymentID, $userID, $deliveryAddressID);

        // Execute the query และเช็คผลลัพธ์
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        // ปิด statement
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
?>
