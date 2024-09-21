document.addEventListener("DOMContentLoaded", function () {
    console.log("Page Loaded");
    
    let products = []; // Stores product data
    let filteredProducts = []; // Stores filtered product data
    let selectedProduct = null; // Stores the selected product
    let paymentID = generatePaymentID();  // Generate PaymentID
    let orderID = generateOrderID();  // Generate OrderID

    // Fetch product data from the PHP backend
    fetch('get_product.php')
        .then(response => response.json())
        .then(data => {
            products = data; // Store the fetched data
            console.log(products); // Log the product data
            displaySearchResults(products);
        })
        .catch(error => console.error('Error fetching product:', error));

    // Elements
    const searchInput = document.getElementById("search-input");
    const searchIcon = document.getElementById("search-icon");
    const sortButtons = document.querySelectorAll(".sort-button");
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const sortByContainer = document.querySelector(".sort-by");
    const categoriesContainer = document.getElementById("categories-container");
    const categoriesHeader = document.querySelector(".P_cat-container");
    const carouselContainer = document.getElementById("carousel-container");
    const orderForm = document.getElementById("order-form");
    const deliveryForm = document.querySelector(".delivery-form"); 
    const cancelButton = document.getElementById("cancel-button");
    const productDetailsContainer = document.getElementById("search-results");
    const paymentConfirmation = document.getElementById("payment-confirmation");
    const completedTransaction = document.getElementById("completed-transaction");

    productDetailsContainer.style.display = "none";

    // Event listeners for category filters
    const categoryElements = document.querySelectorAll(".category");
    categoryElements.forEach(categoryElement => {
        categoryElement.addEventListener("click", function () {
            const selectedCategory = categoryElement.getAttribute("data-category");
            currentCategory = selectedCategory; // Update the current category
            displayProductsByCategory(selectedCategory);
        });
    });

    function displayProductsByCategory(category) {
        const filtered = products.filter(product => product.categories === category);
        displaySearchResults(filtered);

        // Show/hide UI elements based on the selected category
        sortByContainer.style.display = "block";
        categoriesContainer.style.display = "none";
        categoriesHeader.style.display = "none";
        carouselContainer.style.display = "none";
    }

    function showSuggestions() {
        const inputValue = searchInput.value.trim().toLowerCase();
        const suggestions = document.getElementById("suggestions");

        productDetailsContainer.style.display = "block";

        if (inputValue === "") {
            suggestions.style.display = "none";
            return;
        }

        const filteredProducts = products.filter(product =>
            product.ProductName.toLowerCase().includes(inputValue)
        );

        filteredProducts.sort((a, b) => a.ProductName.toLowerCase().indexOf(inputValue) - b.ProductName.toLowerCase().indexOf(inputValue));

        suggestions.innerHTML = "";
        filteredProducts.forEach(product => {
            const suggestionItem = document.createElement("div");
            suggestionItem.classList.add("suggestion-item");
            suggestionItem.textContent = product.ProductName;
            suggestionItem.addEventListener("click", function () {
                searchInput.value = product.ProductName;
                handleSearch();
                hideSuggestions();
            });
            suggestions.appendChild(suggestionItem);
        });

        suggestions.style.display = "block";
    }

    function hideSuggestions() {
        const suggestions = document.getElementById("suggestions");
        suggestions.style.display = "none";
    }

    searchInput.addEventListener("input", showSuggestions);
    searchInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            handleSearch();
            hideSuggestions();
        }
    });
    searchIcon.addEventListener("click", function () {
        handleSearch();
        hideSuggestions();
    });

    function handleSearch() {
        const query = searchInput.value.trim().toLowerCase();
        if (query === "") {
            filteredProducts = products;
            displaySearchResults(filteredProducts);
            sortByContainer.style.display = "block";
            categoriesContainer.style.display = "block";
            categoriesHeader.style.display = "block";
            carouselContainer.style.display = "block";
        } else {
            filteredProducts = products.filter(product =>
                product.ProductName.toLowerCase().includes(query)
            );
            filteredProducts.sort((a, b) => a.ProductName.toLowerCase().indexOf(query) - b.ProductName.toLowerCase().indexOf(query));
            displaySearchResults(filteredProducts);
            sortByContainer.style.display = "block";
            categoriesContainer.style.display = "none";
            categoriesHeader.style.display = "none";
            carouselContainer.style.display = "none";
        }
    }

    function sortResults(sortCriteria) {
        let sortedProducts = [...filteredProducts];

        switch (sortCriteria) {
            case "relevance":
                sortedProducts.sort((a, b) => b.relevance - a.relevance);
                break;
            case "latest":
                sortedProducts.sort((a, b) => b.latest - a.latest);
                break;
            case "top-sales":
                sortedProducts.sort((a, b) => b.topSales - a.topSales);
                break;
            case "price-low-high":
                sortedProducts.sort((a, b) => a.ProductPrice - b.ProductPrice);
                break;
            case "price-high-low":
                sortedProducts.sort((a, b) => b.ProductPrice - a.ProductPrice);
                break;
        }

        displaySearchResults(sortedProducts);
    }

    sortButtons.forEach(button => {
        button.addEventListener("click", function (event) {
            const sortCriteria = event.target.getAttribute("data-sort");
            sortResults(sortCriteria);
            setActiveSortButton(event.target);
        });
    });

    dropdownItems.forEach(item => {
        item.addEventListener("click", function (event) {
            const sortCriteria = event.target.getAttribute("data-sort");
            sortResults(sortCriteria);
            setActiveSortButton(event.target.closest(".dropdown"));
        });
    });

    function setActiveSortButton(button) {
        sortButtons.forEach(btn => btn.classList.remove("active"));
        dropdownItems.forEach(item => item.classList.remove("active"));
        button.classList.add("active");
    }
    
    function displaySearchResults(products) {
        const resultsContainer = document.getElementById("search-results");
        resultsContainer.innerHTML = "";
    
        if (products.length === 0) {
            const noResultsMessage = document.createElement("p");
            noResultsMessage.textContent = "ไม่มีสินค้าตรงกับที่คุณค้นหา";
            noResultsMessage.classList.add("no-results-message");
            resultsContainer.appendChild(noResultsMessage);
        } else {
            products.forEach(product => {
                const resultItem = document.createElement("div");
                resultItem.classList.add("result-item");
    
                // Use base64 encoded image if available
                const imageUrl = product.image_product ? product.image_product : 'default-image.jpg'; // Check if image_product exists
    
                resultItem.innerHTML =
                    `<img src="${imageUrl}" alt="${product.ProductName}">
                    <h6>${product.ProductName}</h6>
                    <p>฿${product.ProductPrice}</p>`;
    
                resultItem.addEventListener("click", () => {
                    displayProductDetails(product);
                });
    
                resultsContainer.appendChild(resultItem);
            });
        }
    }
     
    function displayProductDetails(product) {

        sortByContainer.style.display = "none";
        selectedProduct = product;

        const productDetailsContainer = document.getElementById("search-results");
        productDetailsContainer.innerHTML = "";
    
        const productDetails = document.createElement("div");
        productDetails.id = "product-details";
        productDetails.classList.add("product-details");
    
        // Check if image URL exists
        const imageUrl = product.image_product ? product.image_product : 'default-image.jpg';
    
        let sizeOptions = "";
        if (product.ProductSize) {
            sizeOptions = product.ProductSize.split(',').map(size =>
                `<button class="size-option" data-size="${size}">${size}</button>`).join("");
        }
    
        productDetails.innerHTML =
            `<img src="${imageUrl}" alt="${product.ProductName}">
            <h2>${product.ProductName}</h2>
            <p class="price">฿${product.ProductPrice}</p>
            ${sizeOptions ? 
                `<div class="size-container">
                    <label for="size">Size:</label>
                    <div id="size">${sizeOptions}</div>
                </div>` 
             : ""}
            <label for="quantity">Quantity:</label>
            <div class="quantity-container">
                <button class="quantity-button" id="decrease-quantity">-</button>
                <input type="number" id="quantity" name="quantity" min="1" max="${product.ProductQuantity}" value="1">
                <button class="quantity-button" id="increase-quantity">+</button>
            </div>
            <div class="buttons-container">
                <button class="chat-now-button">Chat Now</button>
                <button class="add-to-cart-button">Add to Cart</button>
                <button class="buy-now-button" id="buy-now-button">Buy Now</button>
            </div>`;
    
        productDetailsContainer.appendChild(productDetails);
    
        // Event listeners for size options
        const sizeButtons = document.querySelectorAll(".size-option");
        sizeButtons.forEach(button => {
            button.addEventListener("click", function () {
                sizeButtons.forEach(btn => btn.classList.remove("selected"));
                button.classList.add("selected");
            });
        });
    
        // Event listeners for quantity buttons
        const quantityInput = document.getElementById("quantity");
        document.getElementById("increase-quantity").addEventListener("click", function () {
            if (quantityInput.value < product.ProductQuantity) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            }
        });
        document.getElementById("decrease-quantity").addEventListener("click", function () {
            if (quantityInput.value > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        });
    
        // Event listener for Buy Now button
        const buyNowButton = document.getElementById("buy-now-button");
        if (buyNowButton) {
            buyNowButton.addEventListener("click", function () {
                console.log('Buy Now button clicked');
                if (selectedProduct) {
                    displayOrderForm(selectedProduct);
                }
            });
        } else {
            console.error('Buy Now button not found');
        }

    }
    
    function displayOrderForm(product) {
        const orderFormContainer = document.getElementById('order-form-container');
        if (orderFormContainer) {
            orderFormContainer.style.display = 'block';
    
            console.log("Displaying order form for:", product);
    
            productDetailsContainer.style.display = "none";
            paymentConfirmation.style.display = "none";
            completedTransaction.style.display = "none";
    
            const orderForm = document.getElementById('order-form');
            if (orderForm) {
                orderForm.style.display = "block";
            } else {
                console.error('Order form not found');
            }
        } else {
            console.error('Order form container not found');
        }
    }

    cancelButton.addEventListener("click", function () {
        orderForm.style.display = "none";
        productDetailsContainer.style.display = "block";
    });

    function showPaymentConfirmationPage(totalPrice, paymentChannel, productName) {
        orderForm.style.display = "none";
        sortByContainer.style.display = "none";
    
        paymentConfirmation.innerHTML = `
            <button id="close-payment-confirmation" data-status="Cancelled"><i class="bi bi-x-circle"></i></button>
            <h2>Payment</h2>
            <p>Total Amount: ฿${totalPrice} baht</p>
            <p>Pay to: ${paymentChannel}</p>
            <p>Payment ID: ${paymentID}</p>
            <p>Product: ${productName}</p>
            <button id="confirm-payment" data-status="Confirmed">Confirm Payment</button>
        `;
    
        paymentConfirmation.style.display = "block";
    
        // ใช้ dataset เพื่อส่งค่า paymentStatus
        document.querySelectorAll("button[data-status]").forEach(button => {
            button.addEventListener("click", function() {
                const status = button.dataset.status;
                submitPaymentData(status);  // ส่งค่าผ่าน dataset
    
                // If payment is confirmed, finalize the order
                if (status === "Confirmed") {
                    finalizeOrder(); // Call finalizeOrder after payment confirmation
                }
            });
        });
    
        document.getElementById("close-payment-confirmation").addEventListener("click", returnToProductDetails);
        document.getElementById("confirm-payment").addEventListener("click", showCompletedTransactionPage);
    }
    


    
    
    function returnToProductDetails() {
        paymentConfirmation.style.display = "none";
        productDetailsContainer.style.display = "block";
        sortByContainer.style.display = "none";
    }

    deliveryForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission
    
        // Prepare data for payment confirmation page
        const totalPrice = calculateTotalPrice(); // Function to calculate total price
        const paymentChannel = document.querySelector('input[name="payment-channel"]:checked').value;
        const productName = selectedProduct ? selectedProduct.ProductName : "Unknown Product";
    
        // Send form data to backend
        const formData = new FormData(deliveryForm);
        formData.append('totalPrice', totalPrice);
        formData.append('paymentChannel', paymentChannel);
        formData.append('paymentID', paymentID); // Use the stored PaymentID
        formData.append('productName', productName);
    
        // Send data to backend
        fetch('delivery_address.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Check server response
            if (data.success) {
                // Show payment confirmation page after data is saved
                showPaymentConfirmationPage(totalPrice, paymentChannel, productName);
                
            } else {
                alert('Error saving data');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    function calculateTotalPrice() {
        // Get the quantity selected by the user
        const quantity = parseInt(document.getElementById("quantity").value);

        // Check if a product is selected and the quantity is valid
        if (selectedProduct && !isNaN(quantity) && quantity > 0) {
          // Multiply the product price by the quantity to get the total price
          return selectedProduct.ProductPrice * quantity;
        } else {
          // Return 0 if no product is selected or quantity is invalid
          return 0;
        }
    }

    function generatePaymentID() {
        // Generate a unique payment ID
        return Math.random().toString(36).substr(2, 9).toUpperCase();
    }

    function showCompletedTransactionPage() {
        // Hide order form and show completed transaction
        paymentConfirmation.style.display = "none";
        document.getElementById('order-form').style.display = 'none';
        document.getElementById('completed-transaction').style.display = 'block';

        // Update the completed transaction page
        document.getElementById('order-number').textContent = orderID;
        document.getElementById('payment-status').textContent = 'finished';
        document.getElementById('shipping-status').textContent = 'Waiting for the seller to send the package to shipping.';
        document.getElementById('transport-name').textContent = 'J&T';

        // finalizeOrder();
        // Close completed transaction button event
        document.getElementById('close-completed-transaction').addEventListener('click', function () {
            document.getElementById('completed-transaction').style.display = 'none';
            returnToindex();
        });
    }

    function returnToindex() {
        // Implement logic to go back to the main page
        window.location.href = 'index.php'; // Replace 'index.php' with your main page URL
    }

// POST หน้า Payment confirm
    paymentConfirmation.addEventListener("click", function(event) {
        const target = event.target;
        if (target.matches("button")) {
            const status = target.getAttribute("data-status");
            if (status) {
                // Disable button to prevent multiple submissions
                target.disabled = true;  // Disable the button here
    
                submitPaymentData(status); // Send payment data once
            }
        }
    });   

    let isSubmitting = false;

    function submitPaymentData(paymentStatus) {
        if (isSubmitting) return;  // Prevent double submission
        isSubmitting = true;  // Set the flag to true
            const paymentChannel = document.querySelector('input[name="payment-channel"]:checked').value;
            const paymentTypeMap = {
                "QR PromptPay": 1,
                "Mobile Banking": 2,
                "Cash on Delivery": 3,
                "Credit/Debit Card": 4
            };
            const paymentTypeID = paymentTypeMap[paymentChannel];
            const totalPrice = calculateTotalPrice();
        
            const formData = new FormData();
            formData.append('totalPrice', totalPrice);
            formData.append('paymentTypeID', paymentTypeID);
            formData.append('paymentID', paymentID); // Use the stored PaymentID
            formData.append('paymentStatus', paymentStatus); // Send the paymentStatus
            formData.append('productName', selectedProduct ? selectedProduct.ProductName : "Unknown Product");
        
            fetch('insert_payment.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            isSubmitting = false;  // Allow future submissions
        })
        .catch(error => {
            isSubmitting = false;  // Allow future submissions even if there's an error
        });
    }
    // Function to generate OrderID (similar to generating PaymentID)
    function generateOrderID() {
        return Math.random().toString(36).substr(2, 9).toUpperCase();  // สร้าง OrderID แบบสุ่ม
    }

    function getPaymentID() {
        return new Promise((resolve, reject) => {
            // ดึง PaymentID จากแหล่งข้อมูลหรือการดำเนินการ
            const paymentID = 'some_payment_id'; // สมมติการดึงข้อมูล
            resolve(paymentID);
        });
    }

    const shippingStatus = 'waiting'; // หรือค่าที่คุณต้องการ
    const shippingName = 'J&T'; // หรือค่าที่คุณต้องการ
    
    function finalizeOrder() {
        console.log("finalizeOrder function called"); // Log เพื่อให้แน่ใจว่าฟังก์ชันนี้ถูกเรียกใช้
        console.log("PaymentID: ", paymentID);
        
        // ใช้ Promise เพื่อรับข้อมูลล่าสุดจาก User และที่อยู่
        Promise.all([
            getLatestUserID(), // ดึง UserID ล่าสุด
            getLatestDeliveryAddressID(), // ดึง DeliveryAddressID ล่าสุด
            getPaymentID()
        ])
        .then(([userID, deliveryAddressID]) => {
            console.log("Retrieved UserID:", userID); // แสดง UserID
            console.log("Retrieved DeliveryAddressID:", deliveryAddressID); // แสดง DeliveryAddressID
    
            if (!userID || !deliveryAddressID) {
                throw new Error('Missing UserID or DeliveryAddressID');
            }
    
            console.log("Generated OrderID:", orderID); // แสดง OrderID
    
            // ใส่ข้อมูลลงตาราง order
            return insertOrder(orderID, paymentID, userID, deliveryAddressID);
        })
        .then(() => {
            console.log("Order inserted successfully.");
    
            // หลังจากใส่ข้อมูลในตาราง order สำเร็จ ใส่ข้อมูลในตาราง orderproduct
            const productID = selectedProduct.ProductID; // สมมติ selectedProduct มีคุณสมบัติ ProductID
            console.log("Selected ProductID:", productID); // แสดง ProductID
    
            if (!orderID || !productID) {
                throw new Error('Missing OrderID or ProductID');
            }
    
            // ใส่ข้อมูลลงตาราง orderproduct
            return insertOrderProduct(orderID, productID, shippingStatus, shippingName);
        })
        .then(() => {
            console.log("Order product inserted successfully.");
    
            // แสดงหน้าการทำธุรกรรมสำเร็จหลังจากข้อมูลถูกเพิ่มลงในฐานข้อมูล
            showCompletedTransactionPage();
        })
        .catch(error => {
            // ถ้ามีข้อผิดพลาดให้แสดงใน console
            console.error('Error finalizing order:', error);
        });
    }
    
    
    

    // Function to fetch the latest UserID from login table
    function getLatestUserID() {
        return fetch('get_latest_user.php')
            .then(response => response.json())
            .then(data => data.UserID);
    }
    

    // Function to fetch the latest DeliveryAddressID
    function getLatestDeliveryAddressID() {
        return fetch('get_latest_address.php')
            .then(response => response.json())
            .then(data => data.DeliveryAddressID);
    }
    

// Function to insert the order into the database
function insertOrder(orderID, paymentID, userID, deliveryAddressID) {
    const formData = new FormData();
    formData.append('OrderID', orderID);  // varchar(20)
    formData.append('PaymentID', paymentID);  // varchar(50)
    formData.append('UserID', userID);  // int(11)
    formData.append('DeliveryAddressID', deliveryAddressID);  // int(11)

    return fetch('insert_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Order inserted successfully');
        } else {
            console.error('Failed to insert order');
        }
        return data;
    });
}



// Function to insert order products into the database
function insertOrderProduct(orderID, productID, shippingStatus, shippingName) {
    const formData = new FormData();
    formData.append('OrderID', orderID);
    formData.append('ProductID', productID);
    formData.append('ShippingStatus', shippingStatus);
    formData.append('ShippingName', shippingName);
    
    console.log("OrderID:", orderID);
    console.log("ProductID:", productID);
    console.log("ShippingStatus:", shippingStatus);
    console.log("ShippingName:", shippingName);

    return fetch('insert_orderproduct.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response from insert_orderproduct.php:', data);
        if (data.success) {
            console.log('Order product inserted successfully');
        } else {
            console.error('Failed to insert order product:', data.error);
        }
        return data;
    });    
}


});