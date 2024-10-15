<?php
 $session_id=   $_SESSION['session_id'];
 $session_name= $_SESSION['session_name'];
 if($session_id == ''){
  header("Location: index.php");

 }

?>
<div class="container">
  <div class="row">
    <div class="col-md-12" style="text-align: center; padding: 20px 0px 20px 0px">
      <span style="font-weight: 100; font-size: 30px;">
        Welcome Back <?php echo $session_name ?>!
      </span>
    </div>
  </div>

  <div class="row mt-5">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <div class="row">
        <div class="<?php echo ($_SESSION['session_role'] == 'Admin') ? 'col-md-3' : 'col-md-2'; ?>" style="text-align:center; ">
          <a href="index.php?page=Dashboard" style="text-decoration: none;color: #938d8d; font-weight: 100;">
          <img src="assets/img/home.png" height="120px"><br>Dashboard</a>
        </div>

        <div class="<?php echo ($_SESSION['session_role'] == 'Admin') ? 'col-md-3' : 'col-md-2'; ?>" style="text-align:center; ">
            <a href="index.php?page=Products" style="text-decoration: none;color: #938d8d; font-weight: 100;">
            <img src="assets/img/products.png" height="120px"><br>Products</a>
        </div>

        <?php if($_SESSION['session_role'] != "Admin"){ ?>
            <div class="col-md-2" style="text-align:center; ">
                <a href="index.php?page=Wishlist" style="text-decoration: none;color: #938d8d; font-weight: 100;">
                <img src="assets/img/wishlist.png" height="120px"><br>Wishlist</a>
            </div>
            <div class="col-md-2" style="text-align:center; ">
                <a href="index.php?page=Cart" style="text-decoration: none;color: #938d8d; font-weight: 100;">
                <img src="assets/img/cart.png" height="120px"><br>My Cart</a>
            </div>
        <?php } ?> 

        <div class="<?php echo ($_SESSION['session_role'] == 'Admin') ? 'col-md-3' : 'col-md-2'; ?>" style="text-align:center; ">
            <a href="index.php?page=Orders" style="text-decoration: none;color: #938d8d; font-weight: 100;">
            <img src="assets/img/orders.png" height="120px"><br>Orders</a>
        </div>

        <div class="<?php echo ($_SESSION['session_role'] == 'Admin') ? 'col-md-3' : 'col-md-2'; ?>" style="text-align:center; ">
            <a href="index.php?page=Logout" style="text-decoration: none;color: #938d8d; font-weight: 100;">
            <img src="assets/img/logout.png" height="120px"><br>Logout</a>
        </div>


    <div class="col-md-2"></div>
  </div>
</div>