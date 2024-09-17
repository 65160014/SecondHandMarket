<?php
    include 'db_connect.php';

    // รับข้อมูลจาก POST
    $orderID = $_POST['OrderID'];
    $productName = $_POST['ProductName']; // รับชื่อสินค้าแทน ProductID
    $shippingStatus = $_POST['ShippingStatus'];
    $shippingName = $_POST['ShippingName'];

    // ค้นหา ProductID จากชื่อสินค้า
    $productQuery = "SELECT ProductID FROM product WHERE ProductName = '$productName'";
    $productResult = mysqli_query($conn, $productQuery);

    if ($productResult && mysqli_num_rows($productResult) > 0) {
        // ดึง ProductID จากผลลัพธ์การ query
        $productRow = mysqli_fetch_assoc($productResult);
        $productID = $productRow['ProductID'];

        // ทำการ insert ข้อมูลในตาราง orderproduct โดยใช้ ProductID ที่หาได้
        $query = "INSERT INTO orderproduct (OrderID, ProductID, ShippingStatus, ShippingName) VALUES ('$orderID', '$productID', '$shippingStatus', '$shippingName')";

        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
    } else {
        // กรณีที่ไม่พบสินค้า
        echo json_encode(['success' => false, 'error' => 'Product not found']);
    }
?>
