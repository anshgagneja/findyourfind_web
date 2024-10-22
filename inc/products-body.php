<?php

$session_id = $_SESSION['session_id'];

if ($session_id == 0) {
  header("Location: index.php");
}elseif ($session_id == '') {
  header("Location: index.php");
}


//We will use these information for updating the product



$pro_name = (isset($_POST['productname'])) ? $_POST['productname'] : "";
$pro_sp = (isset($_POST['sellingprice'])) ? $_POST['sellingprice'] : "";
$pro_rp = (isset($_POST['regularprice'])) ? $_POST['regularprice'] : "";
$pro_desc = (isset($_POST['productdesc'])) ? addslashes($_POST['productdesc']) : "";





if (isset($_POST['Wishlist'])) {
  $pid = $_POST['pid'];
  $uid = $_SESSION['session_id'];
  //echo $uid;die;
  $sqlCheckWishList = "select * from tbl_user_has_wishlist where user_id = '$uid' and product_id = '$pid' ";
  $sqlwish = $conn->prepare("$sqlCheckWishList");
  $sqlwish->execute();
  $result_wish = $sqlwish->fetchAll(PDO :: FETCH_OBJ);
  if(sizeof($result_wish) > 0){
    // remove 
    $sqlremove = $conn->prepare("delete from tbl_user_has_wishlist where user_id = '$uid' and product_id = '$pid' ");
    $sqlremove->execute();
  }else{
    // insert
    $sqlins = $conn->prepare("INSERT INTO tbl_user_has_wishlist(user_id,product_id) VALUES ('$uid' , '$pid') ");
    $sqlins->execute();

  }

}

if (isset($_POST['Cart'])) {
  $pid = $_POST['pid'];
  $uid = $_SESSION['session_id'];
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

  

}

if (isset($_POST['Submit'])) {

  $pid = $_POST['pid'];
  $photo_1 = "";

  $allow = array("jpg", "JPG", "jpeg", "JPEG", "gif", "GIF", "png", "PNG", "pdf", "PDF");
  //1st File
  if($_FILES['photo1']['name'] == "") {
    //echo "No Image"
  } else {

    $photo1=basename($_FILES['photo1']['name']);
    $extension = pathinfo($photo1, PATHINFO_EXTENSION);
    if(in_array($extension,$allow)){
      $target_path = "./assets/img/";


      $photo1 = md5(rand() * time()).'.'.$extension;
      $target_path = $target_path . $photo1;
      if(move_uploaded_file($_FILES['photo1']['tmp_name'], $target_path)){
          $photo_1 = $photo1;
      }
      
    }
  
  }
  
  $photo2 = "";
  $photo3 = "";
  
  if($photo_1 == ""){
        $sql = "UPDATE products SET
        pro_name = '$pro_name',
        pro_sp = '$pro_sp',
        pro_desc = '$pro_desc',
        pro_rp = '$pro_rp' where  id = '$pid' ";
      
        $conn->exec($sql);


        

  }else{
        
        $sql = "UPDATE products SET
        pro_img_1 = '$photo_1',
        pro_name = '$pro_name',
        pro_sp = '$pro_sp',
        pro_desc = '$pro_desc',
        pro_rp = '$pro_rp' where  id = '$pid' ";
      
        $conn->exec($sql);


        $sql = "UPDATE tbl_product_images SET
          img_path = '$photo_1' where product_id = '$pid' ";
        
        $conn->exec($sql);
     
       }          
}

if(isset($_POST['Delete'])){
  $pid = $_POST['pid'];
  $query = "UPDATE products SET available = 0 WHERE id = $pid";

  $sql = $conn->prepare($query);
  $sql->execute();
  header("Location: index.php?page=Products");
}

if(isset($_POST['addItem'])){
    header("Location: index.php?page=Add_Products");
}

//Select Statement.
if(isset($_GET['text'])){
  $text = addslashes($_GET['text']);
  $stmt = "SELECT * FROM products WHERE LOWER(pro_name) like LOWER('%$text%') AND available = 1 
  UNION 
  SELECT * FROM products WHERE LOWER(pro_desc) LIKE LOWER('%$text%') AND available = 1";
} 
else{
  $stmt = "SELECT * FROM products where available = 1";
}
$sql = $conn->prepare($stmt);
$sql->execute();
$result = $sql->fetchAll(PDO :: FETCH_OBJ);

?>


 <div class="container">
      <div class="row">
        <div class="col-md-12" style="text-align:center; padding: 20px 0px 20px 0px">
          <span style="font-weight:100; font-size:30px; ">
            My Product
          </span>
        </div>
      </div>
    </div>

    <div class="container">
       <?php  
            if(count($result) <= 0){
              echo "<b>No results found for '$text'<b>";
            }
            foreach ($result as $data){ 

                $p_id = $data->id;
                $p_name = $data->pro_name;
                $p_sp = $data->pro_sp;
                $p_rp = $data->pro_rp;
                $p_desc = $data->pro_desc;


                // result img
                $result_img = getProductImages($conn,$p_id);


                $uid = $_SESSION['session_id'];
                //echo $uid;die;
                $user_wishlist = "select * from tbl_user_has_wishlist where user_id = '$uid' and product_id = '$p_id' ";
                $sqlwish_list = $conn->prepare("$user_wishlist");
                $sqlwish_list->execute();
                $result_wish_list = $sqlwish_list->fetchAll(PDO :: FETCH_OBJ);
                $is_wish_list = false;
                if(sizeof($result_wish_list) > 0){
                  $is_wish_list = true;
                }


                //echo $uid;die;
                $qty  = 0;
                $user_cart = "select * from tbl_cart where user_id = '$uid' and product_id = '$p_id' ";
                $cart_list = $conn->prepare("$user_cart");
                $cart_list->execute();
                $cart_list = $cart_list->fetchAll(PDO :: FETCH_OBJ);
                
                if(sizeof($cart_list) > 0){
                  $qty  = $cart_list[0]->qty;
                }


              ?>
      <div class="row">
        <div class="col-md-3">
          <p>Product Id  : <?php echo $p_id; ?></p>
          <?php
            if(sizeof($result_img) > 0){
                echo '<img style="text-align:center" src="assets/img/'.$result_img[0]->img_path.'" width="250px">';
            }else{ 
              echo '<img style="text-align:center" src="assets/img/placeholder.png" width="250px">';
            } 
          ?>
          
        </div>
        <div class="col-md-9">
          
           
               <form method="POST" enctype="multipart/form-data">  

            <div class="row">
              
              
              <input name="page" type="hidden" value="Products">
                  <div class="col-md-12 mb-3">
                    <label><strong>Product Name</strong></label>
                    <input type="text" name="productname" class="form-control" value="<?php echo $p_name?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label><strong>Regular Price </strong></label>
                    <input type="text" name="regularprice" class="form-control" value="<?php echo $p_rp?>">
                  </div>


                  <div class="col-md-6 mb-3">
                    <label><strong>Sale Price </strong></label>
                    <input type="text" name="sellingprice" class="form-control" value="<?php echo $p_sp?>">
                  </div>


                  <div class="col-md-12 mb-3">
                    <label><strong>Description : </strong></label>
                     
                    <?php if($_SESSION['session_role'] != "Admin"){ ?>
                    <?php  echo $p_desc; }else { ?>
                      <textarea class="form-control" rows="5" name="productdesc"><?php echo  $p_desc;?></textarea>
                    <?php  } ?>  
                  </div>

                 
                  
                  <?php if($_SESSION['session_role'] != "Admin"){ ?>
                  
                    <div  class="col-md-12 " style="text-align:center;">
                       <input type="hidden" name="pid" value="<?php echo $p_id; ?>">

                       <button name="Wishlist" class="btn btn-warning"><?php echo ($is_wish_list) ? "Remove From Wishlist" : "Add To Wishlist" ?> </button>
                       <button name="Cart" class="btn btn-success"> Add To Cart <?php echo ($qty > 0) ? "(".$qty.")" : "" ?> </button>
                    </div>
                  
                  <?php } else { ?>
                    
                     <div class="col-md-4 mb-3">
                      <input type="file" name="photo1" class="form-control">
                      
                    </div>

                    <input type="hidden" name="pid" value="<?php echo $p_id;?>" class="form-control">

                    <!-- <div class="col-md-4 mb-3">
                      <input type="file" name="photo2" class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                      <input type="file" name="photo3" class="form-control">
                    </div> -->

                    <div class="col-md-12 " style="text-align:center;">
                       <button name="Submit" class="btn btn-warning"> Update Product </button>
                      <button name="Delete" class="btn btn-warning" style="background-color: red; border: none; margin-left: 15px;"> Remove Product </button>

                    </div>
                  
                  <?php } ?>
            </div>
</form>          
        </div>    
      </div>
      <hr>
       <?php  }  ?>
    </div>
    <?php if($_SESSION['session_role'] == "Admin"){ ?>
        <form method="POST" class="col-md-12 " style="text-align:center;">
            <button name="addItem" type="submit" class="btn btn-warning" style="margin: 15px; background-color: #29AB87; border: none;"> Add new product </button>
        </form>
    <?php } ?>