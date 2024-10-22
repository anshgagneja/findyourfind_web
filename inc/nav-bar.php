<?php
if(isset($_POST['searchBtn'])){
  $text = $_POST['searchTxt'];
  header("Location: index.php?page=Products&text=$text");
}
?>

<nav class="navbar navbar-expand-lg " style="background-color: #08AEEA;
    background-image: linear-gradient(0deg, #08AEEA 0%, #2AF598 100%); height: 75px;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"><img src="assets/img/logo.png" height="80px"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php?page=Dashboard">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php?page=Products">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php?page=Orders">Orders</a>
        </li>
        <?php
          if($_SESSION['session_role'] != "Admin"){ ?>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php?page=Wishlist">Wishlist</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php?page=Cart">Cart</a>
            </li>
          <?php }
        ?>
        
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php?page=Logout">Logout</a>
        </li>
        
      </ul>
      <form class="d-flex" method="POST" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" name="searchTxt">
        <button class="btn btn-outline-success" name="searchBtn" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>