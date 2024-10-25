<?php
    if(isset($_POST['submit'])){
        $name = addslashes($_POST['productname']);
        $desc = addslashes($_POST['productdesc']);
        $reg_price = $_POST['regularprice'];
        $sale_price = $_POST['sellingprice'];
        $qty = $_POST['qty'];

        $uploadDir = 'assets/img/'; 
        $fileName = basename($_FILES['photo']['name']);
        $fileName = str_replace(' ', '_', $fileName);
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileError = $_FILES['photo']['error'];

        if ($fileError === UPLOAD_ERR_OK) {
            $destPath = $uploadDir . $fileName;
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                echo "<p>Error moving the file to the destination folder.</p>";
            }
        } else {
            echo "<p>File upload error: " . $fileError . "</p>";
        }

        $query1 = "INSERT INTO products (pro_name, pro_rp, pro_sp, pro_desc, available) VALUES ('$name', '$reg_price', '$sale_price', '$desc', '$qty')";
        if ($conn->exec($query1) === false) {
            echo "<p>Error inserting product.</p>";
        } else {
            // Retrieve the product ID based on the product name (assuming it's unique)
            $query2 = "SELECT id FROM products WHERE pro_name = '$name' LIMIT 1";
            $stmt = $conn->query($query2);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $pid = $result['id']; 

                $query3 = "INSERT INTO tbl_product_images (product_id, img_path) VALUES ('$pid', '$fileName')";
                if ($conn->exec($query3) === false) 
                    echo "<p>Error inserting image.</p>";
            } else {
                echo "<p>Error retrieving product ID.</p>";
            }
        }

        header("Location: index.php?page=Products");
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FindYourFind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="inc/style.css">
  </head>

  <body>
    <div class="container">
        <span>
            Add Products
        </span>
        <form method="post" class="addProduct" enctype="multipart/form-data">
            <div class="product">
                <div class="imageSection">
                    <img id="imagePreview" src="" alt="Image Preview">
                    <input type="file" class="form-control" id="imageInput" accept="image/*" name='photo'>
                </div>

                <div class="productInfo">
                    <div>
                        <label><strong>Product Name</strong></label>
                        <input type="text" name="productname" class="form-control" required/>
                    </div>

                    <div class="pricingInfo">
                        <div class='prices'>
                            <label><strong>Regular Price </strong></label>
                            <input type="text" name="regularprice" class="form-control" required/>
                        </div>
                        <div class = 'prices' style="margin-left: 25px;">
                            <label><strong>Sale Price </strong></label>
                            <input type="text" name="sellingprice" class="form-control" required />
                        </div>
                    </div>
                    
                    <div>
                        <label><strong>Description : </strong></label>
                        <textarea class="form-control" rows="5" name="productdesc" required></textarea>
                    </div>
                    <div class="quantity">
                        <label><strong>Quantity Available</strong></label>
                        <input type="number" name="qty" class="form-control" required/>
                    </div>
                </div>
            </div>            
            <div class="submission">
                <input type="submit" name='submit' class="btn btn-warning" />
            </div>
        </form>
    </div>

    <script>
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader(); 
                reader.onload = function(e) {
                    imagePreview.src = e.target.result; 
                    imagePreview.style.display = 'block'; 
                }
                reader.readAsDataURL(file); 
            } else {
                imagePreview.style.display = 'none'; 
            }
        });
    </script>

  </body>
</html>
