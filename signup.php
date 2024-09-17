<?php
include 'db_connect.php';

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $FirstName = $_POST['FirstName'];
    $LastName = $_POST['LastName'];
    $Phone = $_POST['Phone'];
    $Birthday = $_POST['Birthday'];
    $Email = $_POST['Email'];
    $Gender = $_POST['Gender'];
    $Password = $_POST['Password'];


    // เริ่มการ transaction
    $conn->begin_transaction();

    try {
        // SQL เพื่อเพิ่มข้อมูลลงในตาราง signup
        $sqlSignup = "INSERT INTO signup (FirstName, LastName, Phone, Birthday, Email, Gender, Password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtSignup = $conn->prepare($sqlSignup);
        if ($stmtSignup === false) {
            throw new Exception("Error preparing the statement for signup: " . $conn->error);
        }

        // ผูกพารามิเตอร์กับคำสั่ง SQL signup
        $stmtSignup->bind_param("sssssss", $FirstName, $LastName, $Phone, $Birthday, $Email, $Gender, $Password);
        
        // ดำเนินการคำสั่ง SQL signup
        if (!$stmtSignup->execute()) {
            throw new Exception("Error executing the statement for signup: " . $stmtSignup->error);
        }

        // SQL เพื่อเพิ่มข้อมูลลงในตาราง user
        $sqlUser = "INSERT INTO user (FirstName, LastName, Phone, Birthday, Email, Gender, Password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtUser = $conn->prepare($sqlUser);
        if ($stmtUser === false) {
            throw new Exception("Error preparing the statement for user: " . $conn->error);
        }

        // ผูกพารามิเตอร์กับคำสั่ง SQL user
        $stmtUser->bind_param("sssssss", $FirstName, $LastName, $Phone, $Birthday, $Email, $Gender, $Password);

        // ดำเนินการคำสั่ง SQL user
        if (!$stmtUser->execute()) {
            throw new Exception("Error executing the statement for user: " . $stmtUser->error);
        }

        // หากทุกอย่างสำเร็จ ให้ commit การทำ transaction
        $conn->commit();

    } catch (Exception $e) {
        // หากเกิดข้อผิดพลาด ย้อนกลับการทำงานทั้งหมด
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อ
    $stmtSignup->close();
    $stmtUser->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงทะเบียน</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D2C8DC;
        }

        .signup-form {
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            margin: 20px auto;
        }

        .signup-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .signup-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .signup-form input[type="text"],
        .signup-form input[type="email"],
        .signup-form input[type="password"],
        .signup-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .signup-form .buttons {
            display: flex;
            justify-content: space-between;
        }

        .signup-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .signup-form .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .signup-form .confirm-btn {
            background-color: #4CAF50;
            color: white;
        }

    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php"><img src="img/LOGO.png" alt="LOGO" width="250px"></a>
        </div>
        <div class="search-bar">
            <div class="input-group mb-3 position-relative">
                <input type="text" class="form-control" placeholder="Search SecondHand" aria-label="Search" aria-describedby="basic-addon2" id="search-input">
                <span class="input-group-text" id="basic-addon2"><i class="bi bi-search" id="search-icon"></i></span>
                <div id="suggestions" class="suggestions position-absolute w-100"></div>
            </div>
        </div>
        <div class="icon-header">
            <i class="bi bi-globe"></i>
            <p>EN</p>
            <i class="bi bi-cart3"></i>
            <i class="bi bi-person-circle"></i>
        </div>        
        <div class="auth-buttons">
            <a href="signup.php"><button>Sign up</button></a>
            <a href="login.php"><button>Login</button></a>
            <a href="selling.php"><button>Selling products</button></a>
        </div>
    </header>

    <form class="signup-form" onsubmit="redirectToLogin(event)" method="POST" action="">
        <h2>Sign up</h2>
        <label for="first-name">First name:</label>
        <input type="text" id="first-name" name="FirstName">
        
        <label for="last-name">Last name:</label>
        <input type="text" id="last-name" name="LastName">

        <label for="birthday">Birthday:</label>
        <input type="date" id="birthday" name="Birthday">

        <label for="gender">Gender:</label>
        <input type="text" id="gender" name="Gender">
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="Email">

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="Phone">

        <label for="password">password:</label>
        <input type="password" id="password" name="Password">
        
        <br><br>
        <div class="buttons">
            <a href="index.php"><button type="button" class="cancel-btn">Cancel</button></a>
            <button type="submit" class="confirm-btn">Confirm order</button> 
        </div>
    </form>
    <div class="contact">
        <a href="#"><i class="bi bi-question-circle"></i></a>
    </div>
    <script src="products.js"></script>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script>
        // ฟังก์ชันที่ใช้ในการแสดงข้อความและเปลี่ยนหน้าไปที่ login.php
        function handleSignup(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มในทันที
            
            // แสดงข้อความแจ้งผู้ใช้ว่าลงทะเบียนสำเร็จ
            alert("คุณลงทะเบียนสำเร็จแล้ว!");

            // เปลี่ยนหน้าไปที่ login.php
            window.location.href = "login.php";
        }
    </script>
</body>
</html>
