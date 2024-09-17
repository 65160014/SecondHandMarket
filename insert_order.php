<?php
    include 'db_connect.php';

    var_dump($_POST)

    $orderID = $_POST['OrderID'];
    $paymentID = $_POST['PaymentID'];
    $userID = $_POST['UserID'];
    $deliveryAddressID = $_POST['DeliveryAddressID'];

    $query = "INSERT INTO order (OrderID, PaymentID, UserID, DeliveryAddressID) VALUES ('$orderID', '$paymentID', '$userID', '$deliveryAddressID')";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
?>
