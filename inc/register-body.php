<?php
ob_start();  // Buffer output to prevent header errors
session_start();
include ('database.php');

$message='';
if (isset($_POST['submit'])) {
  $username=$_POST['yourname'];
  $useremail=$_POST['youremail'];
  $usermobile=$_POST['yourmobile'];
  $userpassword=$_POST['password'];
  $confirmpassword=$_POST['confirmpassword'];
  $encrypted_password=md5($userpassword);
  if ($userpassword==$confirmpassword) {
    $sql = "INSERT INTO users (user_name, user_email, user_mobile,user_password)
  VALUES ('$username', '$useremail', ' $usermobile','$encrypted_password')";
  //use exec() because no results are returned
  $conn->exec($sql);
  $last_id = $conn->lastInsertId();

  header("Location: index.php?page=Thankyou&id=$last_id");
  } else{
    $message= 'Password & Confirm Password Not Matched';
  }
  
}
  
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
                    <?php
                    if($message != ''){
                      echo "<br>";
                      echo "<span style='color:red'>$message</span>";
                    }
                    ?>
                </div>
                <div class="col-md-12">
                  <form method="POST">
                    <div class="row">
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 20px 20px 10px 20px">
                        <label>Your Name</label>
                        <input type="text" required name="yourname" placeholder="Name"
                        class="form-control">
                      </div>
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                        <label>Your Email (Email will be the username)</label>
                        <input type="email" required name="youremail" placeholder="you@yourdomain.com"
                        class="form-control">
                      </div>
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                        <label>Your Mobile</label>
                        <input type="tel" required pattern ="[0-9]{10}" name="yourmobile" placeholder="10 digit mobile number"
                        class="form-control">
                      </div>
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                        <label>Your Password</label>
                        <input type="password" required name="password" placeholder="Password"
                        class="form-control">
                      </div>
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                        <label>Confirm Password</label>
                        <input type="password" name="confirmpassword" required placeholder="Confirm Password"
                        class="form-control">
                      </div>
                      <div class="col-md-12" style="text-align: center; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                     <!--  <a href="authenticate.html" class="btn btn-warning">Register now</a> -->

                     <button name="submit" class="btn btn-warning">Register now</button>
                      </div>

                      <div class="col-md-12" style="text-align: center; font-size: 14px; font-weight: 200; padding: 0px 20px 10px 20px">
                       <div class="row">
                         <div class="col-md-12" style="text-align: center;
                         font-size: 12px;
                         font-weight:100 ">
                           <a href="index.php" style="text-decoration: none; color: black;">
                           Already have an account? Login Now</a>
                         </div>
                       </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-md-4"></div>
      </div>
    </div>

    <!-- Admin Panel HTML codes will be written here (End)-->