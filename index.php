<?php
// Start the session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'database.php';

// Get the page name from the URL
$page_name = isset($_GET['page']) ? $_GET['page'] : "";

// Prevent any unexpected output before headers are sent
ob_start();

if ($page_name == 'Register') {
    include 'inc/header.php';
    include 'inc/register-body.php';
    include 'inc/footer.php';
} elseif ($page_name == 'Thankyou') {
    include 'inc/header.php';
    include 'inc/thankyou-body.php';
    include 'inc/footer.php';
} elseif ($page_name == 'Forgot-Password') {
    include 'inc/header.php';
    include 'inc/forgot-password-body.php';
    include 'inc/footer.php';
} elseif ($page_name == 'Logout') {
    include 'inc/header.php';
    include 'inc/logout-body.php';
    include 'inc/footer.php';
} elseif ($page_name == 'Authenticate') {
    include 'inc/header.php';
    include 'inc/authenticate-body.php';
    include 'inc/footer.php';
} elseif ($page_name == 'Dashboard') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/dashboard-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'Products') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/products-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'Orders') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/orders-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'Cart') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/cart-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'Checkout') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/checkout-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'Wishlist') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/wishlist-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'Add_Products') {
    include 'inc/nav-bar.php';
    include 'inc/add-product-body.php';
    include 'inc/footer-1.php';
} elseif ($page_name == 'History') {
    include 'inc/header-1.php';
    include 'inc/nav-bar.php';
    include 'inc/order_history.php';
    include 'inc/footer-1.php';
} else {
    include 'inc/header.php';
    include 'inc/login-body.php';
    include 'inc/footer.php';
}

ob_end_flush(); // Flush the output buffer to prevent "Headers already sent" errors
?>
