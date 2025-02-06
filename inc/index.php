<?php
session_start();
include __DIR__ . '/database.php';  // Ensure database.php is included correctly

$page_name = isset($_GET['page']) ? $_GET['page'] : "";

if ($page_name == 'Register') {
    include __DIR__ . '/inc/header.php';
    include __DIR__ . '/inc/register-body.php';
    include __DIR__ . '/inc/footer.php';
} elseif ($page_name == 'Thankyou') {
    include __DIR__ . '/inc/header.php';
    include __DIR__ . '/inc/thankyou-body.php';
    include __DIR__ . '/inc/footer.php';
} elseif ($page_name == 'Forgot-Password') {
    include __DIR__ . '/inc/header.php';
    include __DIR__ . '/inc/forgot-password-body.php';
    include __DIR__ . '/inc/footer.php';
} elseif ($page_name == 'Logout') {
    include __DIR__ . '/inc/header.php';
    include __DIR__ . '/inc/logout-body.php';
    include __DIR__ . '/inc/footer.php';
} elseif ($page_name == 'Authenticate') {
    include __DIR__ . '/inc/header.php';
    include __DIR__ . '/inc/authenticate-body.php';
    include __DIR__ . '/inc/footer.php';
} elseif ($page_name == 'Dashboard') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/dashboard-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'Products') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/products-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'Orders') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/orders-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'Cart') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/cart-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'Checkout') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/checkout-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'Wishlist') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/wishlist-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'Add_Products') {
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/add-product-body.php';
    include __DIR__ . '/inc/footer-1.php';
} elseif ($page_name == 'History') {
    include __DIR__ . '/inc/header-1.php';
    include __DIR__ . '/inc/nav-bar.php';
    include __DIR__ . '/inc/order_history.php';
    include __DIR__ . '/inc/footer-1.php';
} else {
    include __DIR__ . '/inc/header.php';
    include __DIR__ . '/inc/login-body.php';
    include __DIR__ . '/inc/footer.php';
}
?>
