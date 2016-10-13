<?php
    session_start();
    require_once("db_config.php");
?>
<!DOCTYPE html>

<html>

<head>
    <title>Spent Vouchers -- Shop</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/shop.css" />
</head>

<body>
    <?php
        include("header.inc");
    ?>

    <div id="main">
    <div class="shell">

    <h1>Spent Vouchers</h1><br>

    <?php
        $query = "SELECT * FROM spent_vouchers";
        $results = $voucher_db_conn->query($query);
        $count = $results->num_rows;
        
        if ($count > 0)
        {
            echo "<table>";
            echo "<tr><th>Voucher ID</th></tr>";
            while ($voucher = $results->fetch_array())
            {
                echo "<tr><td>{$voucher['voucher_id']}</td></tr>";
            }
            echo "</table>";
        }
        else
        {
            echo "No spent vouchers.";
        }
    ?>

    </div>
    </div>

    <?php 
        include("footer.inc");
    ?>
</body>

</html>
