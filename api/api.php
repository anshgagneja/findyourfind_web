<?php

include('cors.php');
include('../database.php');

// Retrieve the username and password from the POST request
//$username = $_POST['username'];
//$password = $_POST['password'];

// Encrypt the password using MD5
//$encryptedPassword = md5($password);
$baseurl = "https://ansh.khalsainfosoft.com/assets/img/";
$response = [];

$action = (isset($_REQUEST['action'])) ?  $_REQUEST['action'] : "";


if($action == "PLACE_ORDER_USER"){


      $address = $_POST['address'];
      $state = $_POST['state'];
      $city = $_POST['city'];
      $zip = $_POST['zip'];
      $uid = $_POST['user_id'];



      $useraddress = getCustomerAddress($conn,$uid);

      
     if(sizeof($useraddress) <= 0) {
        $sql = "INSERT INTO tbl_address (
             customer_id,
             address,
             state,
             city,
             zip) values (
             '$uid',
             '$address',
             '$state',
             '$city',
             '$zip')
             ";

            
             $conn->exec($sql);
     }else{
        $address_id = $useraddress[0]->address_id;
        $sql = "UPDATE tbl_address SET
             customer_id = '$uid',
             address = '$address',
             state = '$state',
             city = '$city',
             zip = '$zip' where address_id = '$address_id' ";
             $conn->exec($sql);
     }


     $user_cart = "select * from tbl_cart LEFT JOIN products on tbl_cart.product_id = `products`.`id` where user_id = '$uid'";
     $cart_list = $conn->prepare("$user_cart");
     $cart_list->execute();
     $cart_list =  $cart_list->fetchAll(PDO :: FETCH_OBJ);



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

      $sqlInsert = "INSERT INTO `tbl_orders`(`order_date`,`title`,`order_details`, `total_amount`, `user_id`, `order_status`) VALUES ('$date','$title','$order_details','$totalAmount','$uid','$status')";


      $query = $conn->prepare($sqlInsert);
      $query->execute();


      $sqlremove = $conn->prepare("delete from tbl_cart where user_id = '$uid' ");
      $sqlremove->execute();

    $response = [
                'success' => true,
                'message' => 'Order Placed',
                'data' => "true"
           ];


}else if($action == "REGISTER"){

  $useremail = $_POST['useremail'] ;
  $userpassword = $_POST['password'];
  $encrypted_password = md5($userpassword);
  $mobile = $_POST['mobile'] ;
  $name = $_POST['name'];
  


  $select_user = "SELECT * FROM users WHERE user_email='$useremail' OR user_mobile='$mobile'";
   $sql = $conn->prepare($select_user);
   $sql->execute();
   $data = $sql->fetchAll(PDO::FETCH_OBJ);

   if(sizeof($data) > 0){
        $response = [
                'success' => false,
                'message' => 'Email or mobile already exists',
                'data' => "false"
           ];
   }else{

        $sqlins = $conn->prepare("INSERT INTO users( `user_name`, `user_email`, `user_mobile`, `user_password`) VALUES ('$name' , '$useremail', '$mobile', '$encrypted_password') ");
        $sqlins->execute();
            
        $response = [
                'success' => true,
                'message' => 'Register success',
                'data' => "true"
           ];
   }
    


}else if($action == "LOGIN"){

  $useremail = $_POST['useremail'] ;
  $userpassword = $_POST['password'];
  $encrypted_password = md5($userpassword);

  $select_user = "SELECT * FROM users WHERE user_email='$useremail'AND user_password='$encrypted_password'";
   $sql = $conn->prepare($select_user);
   $sql->execute();
   $data = $sql->fetchAll(PDO::FETCH_OBJ);

   if(sizeof($data) > 0){
        $response = [
                'success' => true,
                'message' => 'Login success',
                'data'=>$data
           ];
   }else{
        $response = [
            
                'data' => [],
                'success' => false,
                'message' => 'Invalid username or password'
           ];
   }
    


}else if($action == "PRODUCTS"){
    $user_id = $_POST['user_id'] ;    
    $stmt = "SELECT * FROM products ";
    $sql = $conn->prepare("$stmt");
    $sql->execute();
    $result = $sql->fetchAll(PDO :: FETCH_OBJ);
    foreach ($result as $resultObj) {
          $resultObj->is_wish_list = false;  
          $resultObj->main_image = $baseurl.'assets/img/placeholder.png';

          $sqlCheckWishList = "select * from tbl_user_has_wishlist where user_id = '$user_id' and product_id = '$resultObj->id' ";
          $sqlwish = $conn->prepare("$sqlCheckWishList");
          $sqlwish->execute();
          $result_wish = $sqlwish->fetchAll(PDO :: FETCH_OBJ);
          if(sizeof($result_wish) > 0){
            $resultObj->is_wish_list = true;
          }

          $result_img = getProductImages($conn,$resultObj->id);
          if(sizeof($result_img) > 0){
             $resultObj->main_image = $baseurl.$result_img[0]->img_path;
          }

    }

    $response = [
                'success' => true,
                'message' => 'Login success',
                'data'=>$result
           ];



}else if($action == "FAVORITES"){

    $user_id = $_POST['user_id'] ;    
    $stmt = "select * from tbl_user_has_wishlist LEFT JOIN products on tbl_user_has_wishlist.product_id = products.id where user_id = '$user_id'";
    $sql = $conn->prepare("$stmt");
    $sql->execute();
    $result = $sql->fetchAll(PDO :: FETCH_OBJ);
    foreach ($result as $resultObj) {
          $resultObj->is_wish_list = true;  
          $resultObj->main_image = $baseurl.'assets/img/placeholder.png';
          
          $result_img = getProductImages($conn,$resultObj->id);
          if(sizeof($result_img) > 0){
             $resultObj->main_image = $baseurl.$result_img[0]->img_path;
          }

    }

    $response = [
                'success' => true,
                'message' => 'Login success',
                'data'=>$result
           ];

}else if($action == "UPDATE_FAV_UNFAV"){

    $uid = $_POST['user_id'] ;    
    $pid = $_POST['pid'];
    $sqlCheckWishList = "select * from tbl_user_has_wishlist where user_id = '$uid' and product_id = '$pid' ";
      $sqlwish = $conn->prepare("$sqlCheckWishList");
      $sqlwish->execute();
      $result_wish = $sqlwish->fetchAll(PDO :: FETCH_OBJ);
    if(sizeof($result_wish) > 0){
        // remove 
        $sqlremove = $conn->prepare("delete from tbl_user_has_wishlist where user_id = '$uid' and product_id = '$pid' ");
        $sqlremove->execute();
        $fav = false;
    }else{
        // insert
        $sqlins = $conn->prepare("INSERT INTO tbl_user_has_wishlist(user_id,product_id) VALUES ('$uid' , '$pid') ");
        $sqlins->execute();
        $fav = true;

    }

    $response = [
                'success' => true,
                'message' => 'Success',
                'data'=>$fav
           ];

}else if($action == "ADD_TO_CART"){

    $uid = $_POST['user_id'] ;    
    $pid = $_POST['pid'];
  //echo $uid;die;
  $sqlCheckWishList = "select * from tbl_cart where user_id = '$uid' and product_id = '$pid' ";
  $sqlwish = $conn->prepare("$sqlCheckWishList");
  $sqlwish->execute();
  $result_wish = $sqlwish->fetchAll(PDO :: FETCH_OBJ);
  $qty = 1;
  if(sizeof($result_wish) > 0){
    // remove 
    $qty = $qty+1;
    //'$qty'
    $sqlins = $conn->prepare("UPDATE tbl_cart set qty = '$qty' where user_id = '$uid' AND product_id = '$pid' ");
  }else{
    $sqlins = $conn->prepare("INSERT INTO tbl_cart(user_id,product_id,qty) VALUES ('$uid' , '$pid', '$qty') ");
  }
  // insert
  
  $sqlins->execute();

    $response = [
                'success' => true,
                'message' => 'Success',
                'data'=>true
           ];

}else if($action == "CART_LIST"){

    $user_id = $_POST['user_id'] ;    
    $user_cart = "select * from tbl_cart LEFT JOIN products on tbl_cart.product_id = `products`.`id` where user_id = '$user_id'";
     $cart_list = $conn->prepare("$user_cart");
     $cart_list->execute();
     $cart_data = $cart_list->fetchAll(PDO :: FETCH_OBJ);
     $totalAmount = 0;
     foreach ($cart_data as $obj) {
        $totalAmount+= $obj->qty * $obj->pro_sp;

        $obj->main_image = $baseurl.'assets/img/placeholder.png';
          
          $result_img = getProductImages($conn,$obj->id);
          if(sizeof($result_img) > 0){
             $obj->main_image = $baseurl.$result_img[0]->img_path;
          }
          
     }
    $response = [
                'success' => true,
                'message' => 'Login success',
                'data'=>['data'=>$cart_data,'total'=>$totalAmount]
           ];

}else if($action == "ORDER_LIST"){
    $uid = $_POST['user_id'] ;    
    $order_list = getUserOrders($conn,$uid);
    $response = [
                'success' => true,
                'message' => 'Login success',
                'data'=>$order_list
           ];

}else if($action == "GET_USER_ADDRESS"){

  $uid = $_POST['user_id'] ;    
  $response = [
                'success' => true,
                'message' => 'Success',
                'data'=>  getCustomerAddress($conn,$uid)
   ];

}



// // Query the database to check if credentials match
// $query = "SELECT * FROM products WHERE id=1";
// $stmt = $conn->prepare($query);
// //$stmt->bindParam(':username', $username);
// //$stmt->bindParam(':password', $encryptedPassword);
// $stmt->execute();
// $product = $stmt->fetch(PDO::FETCH_ASSOC);

// if ($product) {
//     // Valid credentials
//     $response = [
//         'success' => true,
//         'message' => 'Login successful',
//         'pro_name' => $product['pro_name'],
//         'pro_rp' => $product['pro_rp'],
//         'pro_sp' => $product['pro_sp'],
//         'pro_desc' => $product['pro_desc'],
//         'pro_img_1' => $product['pro_img_1'],
//         'pro_img_2' => $product['pro_img_2'],
//         'pro_img_3' => $product['pro_img_3'],
//     ];
// } else {
//     // Invalid credentials
//     $response = [
//         'success' => false,
//         'message' => 'Invalid username or password',
//     ];
// }

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>