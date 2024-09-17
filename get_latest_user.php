<?php
    include 'db_connect.php';
    $query = "SELECT UserID FROM login ORDER BY UserID DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row);
?>
