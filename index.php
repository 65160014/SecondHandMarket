<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $name = $_POST['name'];
    $Phone = $_POST['Phone'];
    $Province = $_POST['Province'];
    $District = $_POST['District'];
    $ZipCode = $_POST['ZipCode'];
    $FurtherDescription = $_POST['FurtherDescription'];
    $payment_channel = $_POST['payment-channel'];

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($name) || empty($Phone) || empty($Province) || empty($District) || empty($ZipCode)) {
        echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกข้อมูลทุกช่อง']);
        exit();
    }

    // เตรียมคำสั่ง SQL สำหรับบันทึก Order ลงในฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO orders (name, Phone, Province, District, ZipCode, FurtherDescription, PaymentChannel) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $Phone, $Province, $District, $ZipCode, $FurtherDescription, $payment_channel);

    if ($stmt->execute()) {
        // ดึง OrderID ล่าสุดที่เพิ่งถูกสร้างขึ้น
        $orderID = $stmt->insert_id;  // insert_id จะดึงค่า ID ของคำสั่ง SQL ล่าสุด

        // ส่ง OrderID กลับมาในรูปแบบ JSON
        echo json_encode(['status' => 'success', 'orderID' => $orderID]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
    }

    $stmt->close();
    $conn->close();
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecondHand Market</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css" rel="stylesheet">
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
            <a href="#"><i class="bi bi-globe"></i></a>
            <p>EN</p>
            <a href="#"><i class="bi bi-cart3"></i></a>
            <a href="#"><i class="bi bi-person-circle"></i></a>
        </div>        
        <div class="auth-buttons">
            <a href="signup.php"><button>Sign up</button></a>
            <a href="login.php"><button>Login</button></a>
            <a href="selling.php"><button>Selling products</button></a>
        </div>
    </header>

    <main>
        
        <div class="sort-by">
            <label for="sort-options">Sort By:</label>
            <div class="sort-buttons">
                <button class="sort-button" data-sort="relevance">Relevance</button>
                <button class="sort-button" data-sort="latest">Latest</button>
                <button class="sort-button" data-sort="top-sales">Top Sales</button>
                <div class="dropdown">
                    <button class="sort-button dropdown-toggle" data-sort="price">Price</button>
                    <div class="dropdown-menu">
                        <button class="dropdown-item" data-sort="price-low-high">Low to High</button>
                        <button class="dropdown-item" data-sort="price-high-low">High to Low</button>
                    </div>
                </div>
            </div> 
        </div>

        <div class="carousel-container" id="carousel-container">
            <div id="carouselExampleIndicators" class="carousel slide">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="img/promotion_1.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="img/promotion_2.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="img/promotion_3.png" class="d-block w-100" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="P_cat-container">
            <p>Categories</p>
        </div>

        <div class="categories-container" id="categories-container">
            <div id="categories" class="categories">
                <div class="category" data-category="gaming">
                    <img src="img/cat_gaming.jpg" alt="Gaming">
                    <p>Gaming</p>
                </div>
                <div class="category" data-category="shoe">
                    <img src="img/cat_shoe.jpg" alt="Shoe">
                    <p>Shoe</p>
                </div>
                <div class="category" data-category="clothes">
                    <img src="img/cat_clothes.jpg" alt="Clothes">
                    <p>Clothes</p>
                </div>
                <div class="category" data-category="clocks">
                    <img src="img/cat_clock.jpg" alt="Clocks">
                    <p>Clocks</p>
                </div>
                <div class="category" data-category="accessories">
                    <img src="img/cat_accessories.jpg" alt="Accessories">
                    <p>Accessories</p>
                </div>
                <div class="category" data-category="sport">
                    <img src="img/cat_sport.jpg" alt="Accessories">
                    <p>Sport</p>
                </div>
                <div class="category" data-category="beauty">
                    <img src="img/cat_beauty.jpg" alt="Accessories">
                    <p>Beauty</p >
                </div>
                <div class="category" data-category="kitchen">
                    <img src="img/cat_kitchen.jpg" alt="Kitchen">
                    <p>Kitchen</p>
                </div>
                <div class="category" data-category="bathroom">
                    <img src="img/cat_bathroom.jpg" alt="Bathroom">
                    <p>Bathroom</p>
                </div>
                <div class="category" data-category="bedroom">
                    <img src="img/cat_bedroom.jpg" alt="Bedroom">
                    <p>Bedroom</p>
                </div>
                <div class="category" data-category="mobile Phone">
                    <img src="img/cat_phone.jpg" alt="Mobile Phone">
                    <p>Mobile Phone</p>
                </div>
                <div class="category" data-category="camera">
                    <img src="img/cat_camera.jpg" alt="Camera">
                    <p>Camera</p>
                </div>
                <div class="category" data-category="pets">
                    <img src="img/cat_pets.jpg" alt="Pets">
                    <p>Pets</p>
                </div>
                <div class="category" data-category="watches&glasses">
                    <img src="img/cat_watches&glasses.jpg" alt="Watches&Glasses">
                    <p>Watches&Glasses</p>
                </div>
            </div>
        </div>

        <div class="search-results-container">
            <div id="search-results" class="search-results"></div>
        </div>


    <div id="order-form-container">    
        <div id="order-form" style="display: none;">
            <h3>Delivery Address</h3>
            <form class="delivery-form" action="process_order.php" method="POST">
                <div>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="phone-number">Phone Number:</label>
                    <input type="text" id="phone-number" name="Phone" required>
                </div>
                <div>
                    <label for="province">Province:</label>
                    <input type="text" id="province" name="Province" required>
                </div>
                <div>
                    <label for="district">District:</label>
                    <input type="text" id="district" name="District" required>
                </div>
                <div>
                    <label for="zip-code">Zip Code:</label>
                    <input type="text" id="zip-code" name="ZipCode" required>
                </div>
                <div>
                    <label for="further-description">Further Description:</label>
                    <textarea id="further-description" name="FurtherDescription"></textarea>
                </div>
        
                <h3>Payment Channels</h3>
                <div>
                    <input type="radio" id="qr-promptpay" name="payment-channel" value="QR PromptPay" required>
                    <label for="qr-promptpay">QR PromptPay</label>
                </div>
                <div>
                    <input type="radio" id="mobile-banking" name="payment-channel" value="Mobile Banking" required>
                    <label for="mobile-banking">Mobile Banking</label>
                </div>
                <div>
                    <input type="radio" id="cash-on-delivery" name="payment-channel" value="Cash on Delivery" required>
                    <label for="cash-on-delivery">Cash on Delivery</label>
                </div>
                <div>
                    <input type="radio" id="credit-debit-card" name="payment-channel" value="Credit/Debit Card" required>
                    <label for="credit-debit-card">Credit/Debit Card</label>
                </div>
        
                <button type="submit" class="btn btn-success">Confirm Order</button>
                <button type="button" id="cancel-button" class="btn btn-danger">Cancel</button>
            </form>
        </div>
    
    <div id="payment-confirmation-container">
        <div id="payment-confirmation" style="display: none;">
    </div>

        <div id="completed-transaction" style="display: none;">
            <button id="close-completed-transaction"><i class="bi bi-x-circle"></i></button>
            <h2>Completed Transaction</h2>
            <div>
                <p><strong>Order number:</strong> <span id="order-number"></span></p>
                <p><strong>Payment:</strong> <span id="payment-status">finished</span></p>
                <div style="margin: 10px 0;">
                    <p><strong>Status:</strong> <span id="shipping-status">Waiting for the seller to send the package to shipping.</span></p>
                    <p>The seller is preparing the package for shipping.</p>
                </div>
                <div>
                    <p><strong>Transport:</strong> <span id="transport-name">J&amp;T</span></p>
                    <p>It is expected that the product will arrive in 1-2 days.</p>
                </div>
            </div>
        </div> 
    </div>            
        

        <div class="contact">
            <a href="#"><i class="bi bi-question-circle"></i></a>
        </div>
    </main>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>