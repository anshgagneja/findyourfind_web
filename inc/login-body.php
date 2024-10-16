<?php
include ('database.php');

if (isset($_POST['submit'])) {

  $useremail = $_POST['useremail'] ;
  $userpassword = $_POST['password'];
  $role = $_POST['role'];
  $encrypted_password = md5($userpassword);

  if($role == "Customer"){
    $select_user = "SELECT * FROM users WHERE user_email='$useremail'AND user_password='$encrypted_password'";
  } else {
    $select_user = "SELECT * FROM tbl_admin WHERE admin_email='$useremail'AND admin_password='$encrypted_password'";
  }
  
  $sql = $conn->prepare($select_user);
  $sql->execute();
  $data = $sql->fetchAll(PDO::FETCH_OBJ);
  foreach($data as $row){
   
    if($role == "Customer"){
      $user_id =$row->id;
      $user_name =$row->user_name;
      $user_email =$row->user_email;
      $user_mobile =$row->mobile;

      if($user_id!=''){
        $_SESSION['session_role'] = $role;
        $_SESSION['session_id'] = $user_id;
        $_SESSION['session_name'] = $user_name;
        $_SESSION['session_email'] = $user_email;
        $_SESSION['session_mobile'] = $user_mobile;

        header("Location: index.php?page=Dashboard");
      }
    } else {
      $user_id =$row->admin_id;
      $user_name ="Admin";
      $user_email =$row->admin_email;
      $user_mobile ="";

      if($user_id!=''){
        $_SESSION['session_role'] = $role;
        $_SESSION['session_id'] = $user_id;
        $_SESSION['session_name'] = $user_name;
        $_SESSION['session_email'] = $user_email;
        $_SESSION['session_mobile'] = $user_mobile;

        header("Location: index.php?page=Dashboard");
      }
    }
  }
}
?>

<!-- Rest of your HTML code -->


  <body>
    <!-- Admin Panel HTML codes will be written here(Starts)-->

    <div class="container-fluid">
      <div class="row" style="padding-top: 10%">
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
                    Login to your account</span>
                </div>
                <div class="col-md-12">
                  <form method="POST">
                    <div class="row">
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 20px 20px 10px 20px">
                        <label>Your Email</label>
                        <input type="email" name="useremail" placeholder="Username"
                        class="form-control">
                      </div>
                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                        <label>Your Password</label>
                        <input type="password" name="password" placeholder="Password"
                        class="form-control">
                      </div>


                      <div class="col-md-12" style="text-align: left; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                        <label>Role</label>
                        <select name="role">
                            <option value="Customer">Customer</option>
                            <option value="Admin">Admin</option>
                        </select>
                        
                      </div>



                      <div class="col-md-12" style="text-align: center; font-size: 14px; font-weight: 200; padding: 10px 20px 10px 20px">
                       <!--<a href="index.php?page=Authenticate" class="btn btn-warning">Login now</a> -->
                       <button name="submit" class="btn btn-warning">Login Now</button>
                      </div>

                      <div class="col-md-12" style="text-align: center; font-size: 14px; font-weight: 200; padding: 0px 20px 10px 20px">
                       <div class="row">
                         <div class="col-md-6" style="text-align: left;
                         font-size: 12px;
                         font-weight:100 ">
                         <a href="index.php?page=Register" style="text-decoration: none; color: black;">
                           No Account? Register Now </a> 

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