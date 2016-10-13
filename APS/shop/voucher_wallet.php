<?php
    session_start();
    require_once("db_config.php");
?>
<!DOCTYPE html>

<html>

<head>
    <title>Voucher Wallet -- Shop</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/shop.css" />
</head>

<body>
    <?php
        include("header.inc");
    ?>

    <div id="main">
    <div class="shell">

    <h1>Voucher Wallet</h1>

    <?php
        $query = "SELECT * FROM vouchers";
        $results = $user_db_conn->query($query);
        $count = $results->num_rows;

        echo "<br /><p>You have {$count} voucher(s).</p><br />";

        if ($count > 0)
        {
            echo "<table>";
            echo "<tr><th>Voucher ID</th><th>Voucher Signature</th></tr>";
            while ($voucher = $results->fetch_array())
            {
                echo "<tr>";
                echo "<td>{$voucher['voucher_id']}</td>";
                echo "<td>{$voucher['voucher_signature']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    ?>

    </div>
    </div>

    <?php 
        include("footer.inc");
    ?>
</body>

</html>
