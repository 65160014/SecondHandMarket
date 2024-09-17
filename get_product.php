<?php
// เชื่อมต่อฐานข้อมูล
include 'db_connect.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    echo json_encode(array("error" => "Connection failed: " . $conn->connect_error));
    exit();
}

// คำสั่ง SQL เพื่อดึงข้อมูลสินค้าทั้งหมด
$sql = "SELECT ProductID, ProductName, ProductDetail, ProductPrice, ProductQuantity, ProductSize, Categories, ProductColor, image_product FROM product";
$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์
if ($result) {
    $products = array();
    
    while ($row = $result->fetch_assoc()) {
        // แปลงรูปภาพจาก BLOB เป็น base64 ถ้ามี
        if (isset($row['image_product']) && !empty($row['image_product'])) {
            $row['image_product'] = base64_encode($row['image_product']);
            $row['image_product'] = 'data:image/jpeg;base64,' . $row['image_product'];
        } else {
            $row['image_product'] = null; // ถ้าไม่มีรูปภาพ
        }

        $products[] = $row; // เพิ่มสินค้าใน array
    }
    
    // ส่งข้อมูลสินค้าในรูปแบบ JSON
    echo json_encode($products);
} else {
    // ส่งข้อความข้อผิดพลาดถ้าการ query ล้มเหลว
    echo json_encode(array("error" => $conn->error));
}

$conn->close();
?>
