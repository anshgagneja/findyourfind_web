<?php
// ✅ Ensure session starts correctly
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include ('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    // ✅ Sanitize input
    $useremail = trim($_POST['useremail']);
    $userpassword = trim($_POST['password']);
    $role = $_POST['role'];
    $encrypted_password = md5($userpassword); // ⚠ Consider using bcrypt for better security

    // ✅ Use prepared statements to prevent SQL injection
    if ($role == "Customer") {
        $query = "SELECT id, user_name, user_email, user_mobile FROM users WHERE user_email = ? AND user_password = ?";
    } else {
        $query = "SELECT admin_id AS id, 'Admin' AS user_name, admin_email, '' AS user_mobile FROM tbl_admin WHERE admin_email = ? AND admin_password = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute([$useremail, $encrypted_password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Handle incorrect login
    if (!$user) {
        echo "<script>alert('Incorrect email/password. Please try again');</script>";
        echo "<script>window.location.href='index.php';</script>";
        exit();
    }

    // ✅ Store session details safely
    $_SESSION['session_role'] = $role;
    $_SESSION['session_id'] = $user['id'];
    $_SESSION['session_name'] = $user['user_name'];
    $_SESSION['session_email'] = $user['user_email'] ?? '';  // Prevent Undefined error
    $_SESSION['session_mobile'] = $user['user_mobile'] ?? '';

    // ✅ Redirect AFTER setting session
    header("Location: index.php?page=Dashboard");
    exit();
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