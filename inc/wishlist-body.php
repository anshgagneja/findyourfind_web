<?php $uid = $_SESSION['session_id'];          

      if(isset($_GET['delete_id'])){
        $id = $_GET['delete_id'];
        deleteWish($conn,$id);
      }
      $wish_list = getuserWish($conn);
?>

<div class="container">
  <div class="row">
    <div class="col-md-12" style="text-align: center; padding: 20px 0px 20px 0px">
      <span style="font-weight: 100; font-size: 30px;">
        My Wishlist
      </span>
    </div>
  </div>

<?php
  if(count($wish_list) == 0){
    echo "<b>No items added to Wishlist<b>";
  } else { ?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Sr No</th>
      <th scope="col">Product Image</th>
      <th scope="col">Product Name</th>
      <th scope="col">Price</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php $counter = 1; 
      foreach ($wish_list as $obj) {
        $p_id = $obj->id;
        $result_img = getProductImages($conn,$p_id);
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
      <td><?php echo $obj->pro_sp; ?></td>
      <td>
          
            <div  class="col-md-12 " style="text-align:center;">
               <a href="?page=Wishlist&delete_id=<?php echo $obj->wishlist_id; ?>" class="btn btn-danger">Delete</a>
            </div>
          
      </td>
    </tr>
   <?php $counter++; } ?>   
    
  </tbody>
</table>
    </div>
  </div>
<?php } ?>
</div>
