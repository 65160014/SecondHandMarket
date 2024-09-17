<?php
include 'db_connect.php';

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $Shippingcost = $_POST['Shippingcost'];
    $SellingProductkeyword = $_POST['SellingProductkeyword'];

    // ตรวจสอบการอัปโหลดไฟล์
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $SellingProductimg = $_FILES['photo']['name']; // ใช้ชื่อไฟล์
        $target_dir = "uploads/"; // โฟลเดอร์ที่ใช้จัดเก็บไฟล์
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
    } else {
        die("Error uploading file: " . $_FILES['photo']['error']);
    }

    // เตรียมคำสั่ง SQL
    $sql = "INSERT INTO products (Shippingcost, SellingProductimg, SellingProductkeyword)
    VALUES (?, ?, ?)";

     // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    // ผูกพารามิเตอร์กับคำสั่ง SQL
    $stmt->bind_param("sss", $Shippingcost, $SellingProductimg, $SellingProductkeyword);

    // ดำเนินการคำสั่ง SQL
    if ($stmt->execute()) {
        echo "Record inserted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selling Products</title>
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

        .selling-form {
            justify-content: center;
            align-items: center;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            margin: 20px auto;
        }

        .selling-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .selling-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .selling-form input[type="text"],
        .selling-form input[type="number"],
        .selling-form textarea,
        .selling-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .selling-form .buttons {
            display: flex;
            justify-content: space-between;
        }

        .selling-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .selling-form .cancel-btn {
            background-color: #f44336;
            color: white;
        }

        .selling-form .submit-btn {
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

    <form class="selling-form" onsubmit="return submitProduct()">
        <h2>Selling Products</h2>
        
        <label for="product-name">Product Name:</label>
        <input type="text" id="product-name" name="product-name" required>

        <label for="details">Details:</label>
        <textarea id="details" name="details" rows="4" required></textarea>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" required>

        <label for="shipping-cost">Shipping Cost:</label>
        <input type="number" id="shipping-cost" name="shipping-cost" required>

        <label for="photo">Add a photo:</label>
        <input type="file" id="photo" name="photo" required>

        <label for="keyword">Key word:</label>
        <input type="text" id="keyword" name="keyword" required>

        <label for="categories">Categories:</label>
        <select id="categories" name="categories" required>
            <option value="">Select</option>
            <option value="gaming">gaming</option>
            <option value="shoes">shoes</option>
            <option value="clothes">clothes</option>
            <option value="clocks">clocks</option>
            <option value="gaming">accessories</option>
            <option value="shoes">sport</option>
            <option value="clothes">beauty</option>
            <option value="gaming">kitchen</option>
            <option value="shoes">bathroom</option>
            <option value="clothes">bedroom</option>
            <option value="clocks">mobile Phone</option>
            <option value="gaming">camera</option>
            <option value="shoes">pets</option>
            <option value="clothes">watches&glasses</option>
            <option value="other">Other</option>
        </select>
        
        <br><br>
        <div class="buttons">
            <a href="index.php"><button type="button" class="cancel-btn">Cancel</button></a>
            <button type="submit" class="submit-btn">Confirm order</button>
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
        function submitProduct() {
            const productName = document.getElementById('product-name').value;
            const details = document.getElementById('details').value;
            const price = document.getElementById('price').value;
            const shippingCost = document.getElementById('shipping-cost').value;
            const keyword = document.getElementById('keyword').value;
            const categories = document.getElementById('categories').value;

            // Simulate product submission (replace with actual backend call)
            alert('Product submitted successfully!');
            window.location.href = 'index.php'; // Redirect to index.php
            return false; // Prevent form submission
        }
    </script>
</body>
</html>
