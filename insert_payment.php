<?php
// Assuming you have a connection to the database
include('db_connect.php');

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);

$totalPrice = isset($_POST['totalPrice']) ? $_POST['totalPrice'] : 0;
$paymentTypeID = isset($_POST['paymentTypeID']) ? $_POST['paymentTypeID'] : 0;
$paymentID = isset($_POST['paymentID']) ? $_POST['paymentID'] : 0;
$paymentStatus = isset($_POST['paymentStatus']) ? $_POST['paymentStatus'] : "Cancelled";
$productName = isset($_POST['productName']) ? $_POST['productName'] : "Unknown Product";

// Check if PaymentID already exists
$check_sql = "SELECT * FROM payment WHERE PaymentID = '$paymentID'";
$result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($result) > 0) {
    // PaymentID already exists, so skip the insertion or update it
    echo json_encode(["success" => false, "message" => "Payment ID already exists"]);
} else {
    // Insert the payment into the database
    $sql = "INSERT INTO payment (PaymentID, PaymentPrice, PaymentTypeID, PaymentStatus)
            VALUES ('$paymentID', '$totalPrice', '$paymentTypeID', '$paymentStatus')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
}

$conn->close();


$conn->close();
?>

