<?php
$session_id = $_SESSION['session_id'];

if ($session_id == 0 || $session_id == '') {
    header("Location: index.php");
}

// Product information for updates
$pro_name = $_POST['productname'] ?? "";
$pro_sp = $_POST['sellingprice'] ?? "";
$pro_rp = $_POST['regularprice'] ?? "";
$pro_desc = isset($_POST['productdesc']) ? addslashes($_POST['productdesc']) : "";
$pro_qty = $_POST['pro_qty'] ?? 0;
$cat_id = $_POST['category_id'] ?? 10;

if (isset($_POST['Wishlist'])) {
    $pid = $_POST['pid'];
    $uid = $_SESSION['session_id'];
    
    $sqlCheckWishList = "SELECT * FROM tbl_user_has_wishlist WHERE user_id = '$uid' AND product_id = '$pid'";
    $sqlwish = $conn->prepare($sqlCheckWishList);
    $sqlwish->execute();
    $result_wish = $sqlwish->fetchAll(PDO::FETCH_OBJ);

    if (sizeof($result_wish) > 0) {
        $sqlremove = $conn->prepare("DELETE FROM tbl_user_has_wishlist WHERE user_id = '$uid' AND product_id = '$pid'");
        $sqlremove->execute();
    } else {
        $sqlins = $conn->prepare("INSERT INTO tbl_user_has_wishlist(user_id, product_id) VALUES ('$uid', '$pid')");
        $sqlins->execute();
    }
}

if (isset($_POST['Cart'])) {
    $pid = $_POST['pid'];
    $uid = $_SESSION['session_id'];
    
    $sqlCheckWishList = "SELECT * FROM tbl_cart WHERE user_id = '$uid' AND product_id = '$pid'";
    $sqlwish = $conn->prepare($sqlCheckWishList);
    $sqlwish->execute();
    $result_wish = $sqlwish->fetchAll(PDO::FETCH_OBJ);
    $qty = sizeof($result_wish) > 0 ? $result_wish[0]->qty + 1 : 1;

    $check_available = "SELECT available FROM products WHERE id = '$pid'";
    $result_available = $conn->prepare($check_available);
    $result_available->execute();
    $result = $result_available->fetchAll(PDO::FETCH_OBJ);
    $available_qty = $result[0]->available; 
    
    if ($qty > $available_qty) {
        echo "<script>alert('Maximum limit reached for this product');</script>";
    }elseif (sizeof($result_wish) > 0) {
        $sqlins = $conn->prepare("UPDATE tbl_cart SET qty = '$qty' WHERE user_id = '$uid' AND product_id = '$pid'");
        $sqlins->execute();
    } else {
        $sqlins = $conn->prepare("INSERT INTO tbl_cart(user_id, product_id, qty) VALUES ('$uid', '$pid', '$qty')");
        $sqlins->execute();
    }
}

if (isset($_POST['Submit'])) {
    $pid = $_POST['pid'];
    $photo_1 = "";

    $allowedExtensions = ["jpg", "jpeg", "gif", "png", "pdf"];
    
    if (!empty($_FILES['photo1']['name'])) {
        $photo1 = basename($_FILES['photo1']['name']);
        $extension = pathinfo($photo1, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($extension), $allowedExtensions)) {
            $target_path = "./assets/img/" . md5(rand() * time()) . '.' . $extension;
            
            if (move_uploaded_file($_FILES['photo1']['tmp_name'], $target_path)) {
                $photo_1 = basename($target_path);
            }
        }
    }
    
    $name = addslashes($pro_name);
    $desc = addslashes($pro_desc);
    $sql = "UPDATE products SET pro_name = '$name', pro_sp = '$pro_sp', pro_desc = '$desc', pro_rp = '$pro_rp', available = '$pro_qty', category_id = '$cat_id'";
    
    $sql .= " WHERE id = '$pid'";
    $conn->exec($sql);

    if ($photo_1) {
        $conn->exec("UPDATE tbl_product_images SET img_path = '$photo_1' WHERE product_id = '$pid'");
    }
}

if (isset($_POST['Delete'])) {
    $pid = $_POST['pid'];
    $sql = $conn->prepare("UPDATE products SET available = 0 WHERE id = $pid");
    $sql->execute();
    header("Location: index.php?page=Products");
}

if (isset($_POST['addItem'])) {
    header("Location: index.php?page=Add_Products");
    exit();
}

if(isset($_POST['History'])){
    $pid = $_POST['pid'];
    header("Location: index.php?page=History&pid=$pid");
    exit();
}

// Select products
if (isset($_GET['text'])) {
    $text = addslashes($_GET['text']);
    $stmt = "SELECT * FROM products WHERE LOWER(pro_name) LIKE LOWER('%$text%') 
             UNION 
             SELECT * FROM products WHERE LOWER(pro_desc) LIKE LOWER('%$text%')
             UNION
             SELECT * FROM products WHERE category_id in (SELECT category_id FROM product_category WHERE LOWER(category) LIKE LOWER('%$text%'))";
} else {
    $stmt = "SELECT * FROM products";
}
$sql = $conn->prepare($stmt);
$sql->execute();
$result = $sql->fetchAll(PDO::FETCH_OBJ);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center py-3">
            <span style="font-weight:100; font-size:30px;">My Product</span>
        </div>
    </div>
</div>

<div class="container">
   <?php  
        if (count($result) <= 0) {
            echo "<b>No results found for '$text'</b>";
        }
        
        foreach ($result as $data) { 
            $p_id = $data->id;
            $p_name = $data->pro_name;
            $p_sp = $data->pro_sp;
            $p_rp = $data->pro_rp;
            $p_desc = $data->pro_desc;
            $pro_qty = $data->available;
            $cat_id = $data->category_id;

            if(is_null($cat_id)){
                $cat_id = 10;
            }
            echo "<script>console.log('$cat_id');</script>";

            $query = $conn->prepare("SELECT category FROM product_category WHERE category_id = '$cat_id'");
            $query->execute();
            $category_name = $query->fetch(PDO::FETCH_OBJ);
            $category = $category_name->category;
            
            $result_img = getProductImages($conn, $p_id);
            $uid = $_SESSION['session_id'];
            $user_wishlist = $conn->prepare("SELECT * FROM tbl_user_has_wishlist WHERE user_id = '$uid' AND product_id = '$p_id'");
            $user_wishlist->execute();
            $is_wish_list = sizeof($user_wishlist->fetchAll(PDO::FETCH_OBJ)) > 0;
            
            $user_cart = $conn->prepare("SELECT * FROM tbl_cart WHERE user_id = '$uid' AND product_id = '$p_id'");
            $user_cart->execute();
            $cart_item = $user_cart->fetch(PDO::FETCH_OBJ);
            $qty = $cart_item ? $cart_item->qty : 0;
          ?>
<div class="row">
    <div class="col-md-3">
        <p>Product Id: <?php echo $p_id; ?></p>
        <img src="assets/img/<?php echo $result_img[0]->img_path ?? 'placeholder.png'; ?>" width="250px" alt="Product Image" style="max-height: 375px;">
    </div>
    <div class="col-md-9">
        <form method="POST" enctype="multipart/form-data">  
            <input name="page" type="hidden" value="Products">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label><strong>Product Name</strong></label>
                    <?php if ($_SESSION['session_role'] != "Admin") { 
                        echo "<p> $p_name </p>";
                    } else { ?>
                        <input type="text" name="productname" class="form-control" value="<?php echo $p_name; ?>">
                    <?php } ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label><strong>Regular Price ($)</strong></label>                    
                    <?php if ($_SESSION['session_role'] != "Admin") { 
                        echo "<p> $p_rp </p>";
                    } else { ?>
                        <input type="number" name="regularprice" class="form-control" value="<?php echo $p_rp; ?>">
                    <?php } ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label><strong>Sale Price ($)</strong></label>                    
                    <?php if ($_SESSION['session_role'] != "Admin") { 
                        echo "<p>$p_sp </p>";
                    } else { ?>
                        <input type="number" name="sellingprice" class="form-control" value="<?php echo $p_sp; ?>">
                    <?php } ?>
                </div>
                <div class="col-md-12 mb-3">
                    <label><strong>Description</strong></label>
                    <?php if ($_SESSION['session_role'] != "Admin") { 
                        echo "<p> $p_desc </p>";
                    } else { ?>
                        <textarea class="form-control" rows="5" name="productdesc"><?php echo $p_desc; ?></textarea>
                    <?php } ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label><strong>Category</strong></label>                    
                    <?php if ($_SESSION['session_role'] != "Admin") { 
                        echo "<p> $category </p>";
                    } else { ?>
                        <select id="categorySelect" name="category_id" class="form-control">
                            <option value="1" <?php if ($cat_id == 1) echo 'selected'; ?>>Electronics</option>
                            <option value="2" <?php if ($cat_id == 2) echo 'selected'; ?>>Furniture</option>
                            <option value="3" <?php if ($cat_id == 3) echo 'selected'; ?>>Clothing</option>
                            <option value="4" <?php if ($cat_id == 4) echo 'selected'; ?>>Books</option>
                            <option value="5" <?php if ($cat_id == 5) echo 'selected'; ?>>Groceries</option>
                            <option value="6" <?php if ($cat_id == 6) echo 'selected'; ?>>Beauty & Health</option>
                            <option value="7" <?php if ($cat_id == 7) echo 'selected'; ?>>Sports & Outdoors</option>
                            <option value="8" <?php if ($cat_id == 8) echo 'selected'; ?>>Home Appliances</option>
                            <option value="9" <?php if ($cat_id == 9) echo 'selected'; ?>>Musical Instruments</option>
                            <option value="10" <?php if ($cat_id == 10) echo 'selected'; ?>>Others</option>
                        </select>
                    <?php } ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label><strong>Quantity Available</strong></label>                    
                    <?php if ($_SESSION['session_role'] != "Admin") { 
                        echo "<p> $pro_qty </p>";
                    } else { ?>
                        <input type="number" name="pro_qty" class="form-control" value="<?php echo $pro_qty; ?>">
                    <?php } ?>
                </div>
                
                <?php if ($_SESSION['session_role'] != "Admin") { 
                    if ($pro_qty > 0) { ?>
                        <div class="col-md-12 text-center">
                            <input type="hidden" name="pid" value="<?php echo $p_id; ?>">
                            <button name="Wishlist" class="btn btn-warning"><?php echo $is_wish_list ? "Remove From Wishlist" : "Add To Wishlist"; ?></button>
                            <button name="Cart" class="btn btn-success"> Add To Cart <?php echo $qty > 0 ? "($qty)" : ""; ?> </button>
                        </div>
                    <?php } else { ?>
                        <p class="text-center text-danger"><b>Product Currently Unavailable</b></p>
                    <?php } 
                } else { ?>
                    <div class="col-md-6 mb-3">
                        <input type="file" name="photo1" class="form-control">
                    </div>
                    <input type="hidden" name="pid" value="<?php echo $p_id; ?>" class="form-control">
                    <div class="col-md-12 text-center">
                        <button name="Submit" class="btn btn-warning"> Update Product </button>
                        <button name="Delete" class="btn btn-danger ml-3"> Remove Product </button>
                        <button name="History" class="btn btn-warning"> View Order History </button>
                    </div>
                <?php } ?>
            </div>
        </form>          
    </div>    
</div>
<hr>
   <?php } ?>
</div>

<?php if ($_SESSION['session_role'] == "Admin") { ?>
    <form method="POST" style="margin-bottom: 15px">
        <div class="container text-center">
            <button name="addItem" class="btn btn-success">Add Item</button>
        </div>
    </form>
<?php } ?>
