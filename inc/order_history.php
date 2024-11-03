<?php
    $pid = isset($_GET['pid']) ? $_GET['pid'] : 0;
    $stmt = $conn->prepare("CALL GetProductOrderDetails('$pid')");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC); 
?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center py-3">
                <span style="font-weight:100; font-size:30px;">Order History for <?php echo $result['product_name'] ?></span>
            </div>
        </div>
    </div>
    <div class="mt-4" style="display: flex; justify-content: center;">
        <table class="table table-bordered" style="max-width: 80%;">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Total Orders</th>
                    <th>Total Quantity Ordered</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($pid) . "</td>";
                    echo "<td>" . htmlspecialchars($result['product_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($result['category']) . "</td>";
                    echo "<td>" . htmlspecialchars($result['total_orders']) . "</td>";
                    echo "<td>" . htmlspecialchars($result['total_qty']) . "</td>";
                    echo "</tr>";
                ?>
            </tbody>
        </table>
    </div>
</body>
