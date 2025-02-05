<?php
// Fetch database credentials from environment variables
$servername = getenv("DB_HOST") ?: "localhost";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") ?: "";
$database = getenv("DB_NAME") ?: "findyourfind";
$port = getenv("DB_PORT") ?: "3306"; // Default MySQL port if not set

try {
    // Connect to the database using PDO
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// chatgpt localhost end

if (!function_exists('getProductImages')) {
    function getProductImages($conn, $p_id) {
        $stmt_img = "SELECT * FROM tbl_product_images WHERE product_id = ?";
        $sql_img = $conn->prepare($stmt_img);
        $sql_img->execute([$p_id]);
        return $sql_img->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('deleteCart')) {
    function deleteCart($conn, $p_id) {
        $sqlremove = $conn->prepare("DELETE FROM tbl_cart WHERE cart_id = ?");
        $sqlremove->execute([$p_id]);
    }
}

if (!function_exists('deleteWish')) {
    function deleteWish($conn, $p_id) {
        $sqlremove = $conn->prepare("DELETE FROM tbl_user_has_wishlist WHERE wishlist_id = ?");
        $sqlremove->execute([$p_id]);
    }
}

if (!function_exists('getuserCart')) {
    function getuserCart($conn) {
        $uid = $_SESSION['session_id'];
        $user_cart = "SELECT * FROM tbl_cart 
                      LEFT JOIN products ON tbl_cart.product_id = products.id 
                      WHERE user_id = ? AND available > 0";
        $cart_list = $conn->prepare($user_cart);
        $cart_list->execute([$uid]);
        return $cart_list->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('getuserWish')) {
    function getuserWish($conn) {
        $uid = $_SESSION['session_id'];
        $user_wishlist = "SELECT * FROM tbl_user_has_wishlist w 
                          INNER JOIN products p ON p.id = w.product_id 
                          WHERE w.user_id = ? AND available > 0";
        $wishlist = $conn->prepare($user_wishlist);
        $wishlist->execute([$uid]);
        return $wishlist->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('getCustomerAddress')) {
    function getCustomerAddress($conn, $user_id) {
        $query = $conn->prepare("SELECT * FROM tbl_address WHERE customer_id = ?");
        $query->execute([$user_id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('getUserOrdersAdmin')) {
    function getUserOrdersAdmin($conn) {
        $query = $conn->prepare("SELECT * FROM tbl_orders 
                                 LEFT JOIN tbl_order_status ON tbl_orders.order_status = tbl_order_status.status_id 
                                 LEFT JOIN users ON tbl_orders.user_id = users.id");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('getUserOrders')) {
    function getUserOrders($conn, $user_id) {
        $query = $conn->prepare("SELECT * FROM tbl_orders 
                                 LEFT JOIN tbl_order_status ON tbl_orders.order_status = tbl_order_status.status_id 
                                 LEFT JOIN users ON tbl_orders.user_id = users.id 
                                 WHERE user_id = ?");
        $query->execute([$user_id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('getLoginUserDetails')) {
    function getLoginUserDetails($conn, $user_id) {
        $query = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $query->execute([$user_id]);
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
}

if (!function_exists('placeOrderUserId')) {
    function placeOrderUserId($conn, $user_id) {
        $cart_list = getuserCart($conn);
        $totalAmount = 0;
        $totalQty = 0;
        $title = "";

        foreach ($cart_list as $obj) {
            $totalAmount += $obj->qty * $obj->pro_sp;
            $totalQty += $obj->qty;
            $p_id = $obj->id;
            $title .= $obj->pro_name . ' ' . $obj->qty . ' ' . $obj->pro_sp . ' ' .
                      "( " . $obj->qty . " * " . $obj->pro_sp . " ) = " . ($obj->qty * $obj->pro_sp) . '<br>';
            
            // Get available stock
            $query = $conn->prepare("SELECT available FROM products WHERE id = ?");
            $query->execute([$p_id]);
            $result = $query->fetch(PDO::FETCH_OBJ);
            $available_qty = $result->available ?? 0;

            $left = max(0, $available_qty - $obj->qty);

            // Update available stock
            $update_query = $conn->prepare("UPDATE products SET available = ? WHERE id = ?");
            $update_query->execute([$left, $p_id]);
        }

        $status = "1";
        $order_details = json_encode($cart_list);
        $date = date("Y-m-d");

        // Insert order
        $sqlInsert = "INSERT INTO tbl_orders (order_date, title, order_details, total_amount, user_id, order_status, order_quantity) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $query = $conn->prepare($sqlInsert);
        $query->execute([$date, $title, $order_details, $totalAmount, $user_id, $status, $totalQty]);

        // Remove from cart
        $sqlremove = $conn->prepare("DELETE FROM tbl_cart WHERE user_id = ?");
        $sqlremove->execute([$user_id]);
    }
}
?>
