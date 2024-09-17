<?php
session_start();
include 'db_connect.php';

// ตรวจสอบว่าฟอร์มถูกส่งหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];

    // ตรวจสอบรหัสผ่านสำหรับการล็อกอิน
    $sql_signup = "SELECT * FROM signup WHERE Email = ?";
    
    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($sql_signup);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // ผูกพารามิเตอร์และดำเนินการ
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $result_signup = $stmt->get_result();
    $signup = $result_signup->fetch_assoc();

    // ตรวจสอบว่าผู้ใช้มีอยู่หรือไม่
    if ($signup) {
        // ตรวจสอบรหัสผ่านที่เข้ารหัส
        if ($Password === $signup['Password']) {
            // เข้าสู่ระบบสำเร็จ
            $_SESSION['signupID'] = $signup['signupID']; // เก็บข้อมูลผู้ใช้ใน session

            // ค้นหา UserID จากตาราง user ที่ตรงกับ Email
            $sql_user = "SELECT UserID FROM user WHERE Email = ?";
            $stmt_user = $conn->prepare($sql_user);
            if ($stmt_user === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }
            $stmt_user->bind_param("s", $Email);
            $stmt_user->execute();
            $result_user = $stmt_user->get_result();
            $user = $result_user->fetch_assoc();

            if ($user) {
                // บันทึกข้อมูลการ login ลงในตาราง login
                $sql_login = "INSERT INTO login (Email, UserID) VALUES (?, ?)";
                $stmt_login = $conn->prepare($sql_login);
                if ($stmt_login === false) {
                    die('Prepare failed: ' . htmlspecialchars($conn->error));
                }
                $stmt_login->bind_param("si", $Email, $user['UserID']);
                $stmt_login->execute();
            }

            // เปลี่ยนเส้นทางไปที่หน้าแรกหรือแดชบอร์ด
            header("Location: index.php");
            exit();
        } else {
            // ข้อมูลรับรองไม่ถูกต้อง
            echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง'); window.location.href='login.php';</script>";
        }
    }

    // ปิดคำสั่งและการเชื่อมต่อ
    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .login-form {
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            margin: 20px auto;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-form .buttons {
            display: flex;
            justify-content: space-between;
        }

        .login-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-form .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .login-form .login-btn {
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

    <form class="login-form" action="login.php" method="POST">
        <h2>Login</h2>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="Email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="Password" required>
        
        <br><br>
        <div class="buttons">
            <a href="index.php"><button type="button" class="cancel-btn">Cancel</button></a>
            <button type="submit" class="login-btn">Login</button>
        </div>
    </form>
    <div class="contact">
        <a href="#"><i class="bi bi-question-circle"></i></a>
    </div>
    <script src="products.js"></script>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
