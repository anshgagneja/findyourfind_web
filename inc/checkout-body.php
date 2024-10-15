<?php $session_id = $_SESSION['session_id'];
   if ($session_id == 0) {
     header("Location: index.php");
   }elseif ($session_id == '') {
     header("Location: index.php");
   }
   $uid = $_SESSION['session_id'];          
   if(isset($_GET['delete_id'])){
     $id = $_GET['delete_id'];
     deleteCart($conn,$id);
   }

   if (isset($_POST['submit'])) {
          $useraddress = getCustomerAddress($conn,$session_id);
          $address = $_POST['address'];
          $state = $_POST['state'];
          $city = $_POST['city'];
          $zip = $_POST['zip'];

          
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


         placeOrderUserId($conn,$uid);
         header("Location: index.php?page=Orders");

         
   }


   $user_cart = getuserCart($conn);
   $useraddress = getCustomerAddress($conn,$session_id);
  
   
   
   
   
   $address_id   =  "";
   $customer_id  =  "";
   $address      =  "";
   $zip          =  "";
   $state        =  "";
   $city         =  "";
   
   
   if(sizeof($useraddress) > 0){
     $address_id   =  $useraddress[0]->address_id;
     $customer_id  =  $useraddress[0]->customer_id;
     $address      =  $useraddress[0]->address;
     $zip          =  $useraddress[0]->zip;
     $state        =  $useraddress[0]->state;
     $city         =  $useraddress[0]->city;
   }
   
   ?>
<div class="container">
   <div class="row">
      <div class="col-md-12" style="text-align:center; padding: 20px 0px 20px 0px">
         <span style="font-weight:100; font-size:30px; ">
         Checkout Address
         </span>
      </div>
   </div>
</div>
<form method="POST">

<div class="container">
   <div class="row">
      <div class="col-md-6">
         <div class="col-md-12 mb-3">
            <label><strong>Address</strong></label>
            <textarea required rows="5" type="text" name="address" class="form-control" ><?php echo $address; ?></textarea>
         </div>
         <div class="col-md-12 mb-3">
            <label><strong>State</strong></label>
            <input required type="text" name="state" class="form-control" value="<?php echo $state;?>">
         </div>
         <div class="col-md-12 mb-3">
            <label><strong>City</strong></label>
            <input required type="text" name="city" class="form-control" value="<?php echo $city;?>">
         </div>
         <div class="col-md-12 mb-3">
            <label><strong>Zip</strong></label>
            <input required type="text" name="zip" class="form-control" value="<?php echo $zip;?>">
         </div>
      </div>
      <div class="col-md-6">
         <table class="table table-striped">
            <thead>
               <tr>
                  <th scope="col">Sr No</th>
                  <th scope="col">Product Image</th>
                  <th scope="col">Product Name</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Price</th>
                  <th scope="col">Total</th>
                  <th scope="col">Action</th>
               </tr>
            </thead>
            <tbody>
               <?php $counter = 1; 
                  $totalAmount = 0;
                  foreach ($user_cart as $obj) {
                  $p_id = $obj->id;
                  $result_img = getProductImages($conn,$p_id);
                  $totalAmount+= $obj->qty * $obj->pro_sp;
                  
                  ?>
               <tr>
                  <th scope="row"><?php echo $counter; ?></th>
                  <td><?php
                     if(sizeof($result_img) > 0){
                         echo '<img style="text-align:center" src="assets/img/'.$result_img[0]->img_path.'" height="50px">';
                     }else{ 
                       echo '<img style="text-align:center" src="assets/img/placeholder.png" height="50px">';
                     } 
                     ?>          
                  </td>
                  <td><?php echo $obj->pro_name; ?></td>
                  <td><?php echo $obj->qty; ?></td>
                  <td><?php echo $obj->pro_sp; ?></td>
                  <td><?php echo "( ".$obj->qty." * ".$obj->pro_sp." ) = ".($obj->qty * $obj->pro_sp); ?></td>
                  <td>
                     <div  class="col-md-12 " style="text-align:center;">
                        <a href="?page=Cart&delete_id=<?php echo $obj->cart_id; ?>" class="btn btn-danger">Delete</a>
                     </div>
                  </td>
               </tr>
               <?php $counter++; } ?>  
               <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>Total</td>
                  <td><?php echo $totalAmount; ?></td>
                  <td>&nbsp;</td>
               </tr>
               <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td colspan="2"><button name="submit" type="submit"  class="btn btn-success">Place Order</button></td>
               </tr>
            </tbody>
         </table>
      </div>
   </div>
   <hr>
</div>
</form>