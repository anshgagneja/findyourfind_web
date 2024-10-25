<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "findyourfind";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
// chatgpt localhost end


if (!function_exists('getProductImages')) {
    function getProductImages($conn,$p_id){
      $stmt_img = "SELECT * FROM tbl_product_images where product_id = '$p_id' ";
      $sql_img = $conn->prepare("$stmt_img");
      $sql_img->execute();
      $result_img = $sql_img->fetchAll(PDO :: FETCH_OBJ);
      return $result_img;
    }
}

if (!function_exists('deleteCart')) {
  function deleteCart($conn,$p_id){
    $sqlremove = $conn->prepare("delete from tbl_cart where cart_id = '$p_id' ");
    $sqlremove->execute();
  }
}

if (!function_exists('deleteWish')) {
  function deleteWish($conn,$p_id){
    $sqlremove = $conn->prepare("delete from tbl_user_has_wishlist where wishlist_id = '$p_id' ");
    $sqlremove->execute();
  }
}

if (!function_exists('getuserCart')) {
  function getuserCart($conn){
     $uid = $_SESSION['session_id'];         
     $user_cart = "select * from tbl_cart LEFT JOIN products on tbl_cart.product_id = `products`.`id` where user_id = '$uid' AND available > 0";
     $cart_list = $conn->prepare("$user_cart");
     $cart_list->execute();
     $cart_list = $cart_list->fetchAll(PDO :: FETCH_OBJ);

     foreach($cart_list as $item){
        $pid = $item->product_id;
        $cart_qty = $item->qty;

        $check_available = "SELECT available FROM products WHERE id = '$pid'";
        $result_available = $conn->prepare($check_available);
        $result_available->execute();
        $result = $result_available->fetchAll(PDO::FETCH_OBJ);
        $available_qty = $result[0]->available; 

        if($available_qty == 0){
            deleteCart($conn, $pid);
        }
        elseif($cart_qty > $available_qty){
            $query = $conn->prepare("UPDATE tbl_cart SET qty = '$available_qty' WHERE user_id = '$uid' AND product_id = '$pid'");
            $query->execute();
        }
     }

     $uid = $_SESSION['session_id'];         
     $user_cart = "select * from tbl_cart LEFT JOIN products on tbl_cart.product_id = `products`.`id` where user_id = '$uid' AND available > 0";
     $cart_list = $conn->prepare("$user_cart");
     $cart_list->execute();
     return $cart_list->fetchAll(PDO :: FETCH_OBJ);
  }
}

if (!function_exists('getuserWish')) {
  function getuserWish($conn){
     $uid = $_SESSION['session_id'];         
     $user_cart = "select * from tbl_user_has_wishlist w inner join products p on p.id = w.product_id
     where w.user_id = '$uid' AND available > 0";
     
     $cart_list = $conn->prepare("$user_cart");
     $cart_list->execute();
     return $cart_list->fetchAll(PDO :: FETCH_OBJ);
  }
}

if (!function_exists('getCustomerAddress')) {
  function getCustomerAddress($conn,$user_id){
    $query = $conn->prepare("select * from tbl_address where customer_id = '$user_id' ");
    $query->execute();
    $result_set = $query->fetchAll(PDO :: FETCH_OBJ);
    return $result_set;
  }
}


if (!function_exists('getUserOrdersAdmin')) {
  function getUserOrdersAdmin($conn,$user_id){
    $query = $conn->prepare("select * from tbl_orders left join tbl_order_status on tbl_orders.order_status = tbl_order_status.status_id LEFT JOIN users on tbl_orders.user_id = users.id  ");
    $query->execute();
    $result_set = $query->fetchAll(PDO :: FETCH_OBJ);
    return $result_set;

  }
}

if (!function_exists('getUserOrders')) {
  function getUserOrders($conn,$user_id){
    $query = $conn->prepare("select * from tbl_orders left join tbl_order_status on tbl_orders.order_status = tbl_order_status.status_id LEFT JOIN users on tbl_orders.user_id = users.id  where user_id = '$user_id' ");
    $query->execute();
    $result_set = $query->fetchAll(PDO :: FETCH_OBJ);
    return $result_set;

  }
} 

if (!function_exists('getLoginUserDetails')) {
  function getLoginUserDetails($conn,$user_id){
    $query = $conn->prepare("select * from users where id = '$user_id' ");
    $query->execute();
    $result_set = $query->fetchAll(PDO :: FETCH_OBJ);
    return $result_set;

  }
} 


if (!function_exists('placeOrderUserId')) {
  function placeOrderUserId($conn,$user_id){
      $cart_list = getuserCart($conn);
      $totalAmount = 0;
      $title = "";
      foreach ($cart_list as $obj) {
        $totalAmount+= $obj->qty * $obj->pro_sp;
        $p_id = $obj->id;
        $title .= $obj->pro_name.' '.$obj->qty.' '.$obj->pro_sp.' '."( ".$obj->qty." * ".$obj->pro_sp." ) = ".($obj->qty * $obj->pro_sp).'<br>';
        $obj->pro_desc = "";
      }  

      $status = "1";
      $order_details = json_encode($cart_list);
      $date = date("Y-m-d");

      $sqlInsert = "INSERT INTO `tbl_orders`(`order_date`,`title`,`order_details`, `total_amount`, `user_id`, `order_status`) VALUES ('$date','$title','$order_details','$totalAmount','$user_id','$status')";


      $query = $conn->prepare($sqlInsert);
      $query->execute();


      $sqlremove = $conn->prepare("delete from tbl_cart where user_id = '$user_id' ");
      $sqlremove->execute();

       

  }
}




?>