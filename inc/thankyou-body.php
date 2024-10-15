<?php
include ('database.php');

$id = $_GET['id'];

$select_user = "SELECT * FROM users WHERE id=$id";
   $sql = $conn->prepare($select_user);
   $sql->execute();
   $data = $sql->fetchAll(PDO::FETCH_OBJ);
   foreach($data as $row)
  //   $database_usename =$row->user_name;
  // echo "$database_usename";
?>
<body>
    <!-- Admin Panel HTML codes will be written here(Starts)-->

    <div class="container-fluid">
      <div class="row" style="padding-top: 5%">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="text-align: center;
            background-color: white; border-radius: 20px;
            padding:10px;">
              <div class="row">
                <div class="col-md-12"  style="padding-bottom: 15px;">
                  <img src="https://static.vecteezy.com/system/resources/thumbnails/011/401/535/small/online-shopping-trolley-click-and-collect-order-logo-design-template-vector.jpg" height="70">
                </div>

                <div class="col-md-12">
                  <span style=" font-weight: 100; font-size: 18px">
                    Register a new account for free</span>
                </div>
                <div class="col-md-12">
                  Dear <?php echo $row->user_name ?>,your registeration is successful.You can now <a href="index.php?page=Login">login</a>
                </div>
              </div>
            </div>
            <div class="col-md-4"></div>
      </div>
    </div>

    <!-- Admin Panel HTML codes will be written here (End)-->