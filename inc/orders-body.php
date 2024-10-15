<?php
  $user_id = $_SESSION["session_id"];
  

  if($_SESSION['session_role'] != "Admin"){
    $order_list = getUserOrders($conn,$user_id);
    //$order_details = getLoginUserDetails($conn,$user_id);

    //oid
    // if(isset($_GET['oid'])){
    //   $oid = $_GET['oid'];
    //   $sqlremove = $conn->prepare("update  tbl_order_status set order_status = '4' where order_id = '$oid' ");
      
    //   $sqlremove->execute();
    // }

  }else{
    $order_list = getUserOrdersAdmin($conn,$user_id);
    //$order_details = getLoginUserDetails($conn,$user_id);
  }




?>
<div class="container">
  <div class="row">
    <div class="col-md-12" style="text-align: center; padding: 20px 0px 20px 0px">
      <span style="font-weight: 100; font-size: 30px;">
        Your Orders
      </span>
    </div>
  </div>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Sr No</th>
      <th scope="col">Date</th>
      <th scope="col">Details</th>
      <th scope="col">Customer</th>
      <th scope="col">Mobile</th>
      <th scope="col">Email</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach ($order_list as $orderObj) { ?>
      <tr>
        <th scope="row">1</th>
        <td><?php echo $orderObj->order_date; ?></td>
        <td><?php echo $orderObj->title; ?></td>
        <td><?php echo $orderObj->user_name;?></td>
        <td><?php echo $orderObj->user_mobile;?></td>
        <td><?php echo $orderObj->user_email;?></td>
        <td><?php echo $orderObj->status_text; ?></td>

      

      </tr>
    <?php  } ?>   
    
    
  </tbody>
</table>
    </div>
  </div>
</div>
