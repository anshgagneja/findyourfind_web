<?php
ob_start(); // Start output buffering to prevent "Headers already sent" issues
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

  if(count($data) == 0){
    echo "<script>alert('Incorrect email/ password. Please try again');</script>";
    echo "<script>window.location.href='index.php';</script>";
    exit;
  } 
  else {
    foreach($data as $row){
    
      if($role == "Customer"){
        $user_id =$row->id;
        $user_name =$row->user_name;
        $user_email =$row->user_email;
        $user_mobile =$row->user_mobile;

        if($user_id!=''){
          $_SESSION['session_role'] = $role;
          $_SESSION['session_id'] = $user_id;
          $_SESSION['session_name'] = $user_name;
          $_SESSION['session_email'] = $user_email;
          $_SESSION['session_mobile'] = $user_mobile;
          
          header("Location: index.php?page=Dashboard");
          exit; // Ensure script stops executing after redirection
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
          exit; // Ensure script stops executing after redirection
        }
      }
    }
  }
}
ob_end_flush();
?>
