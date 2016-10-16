<?php
    session_start();
    require_once("db_config.php");

    $VENDOR_BANK_ACC_NO = 987654;
?>
<!DOCTYPE html>

<html>

<head>
    <title>Shop</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/shop.css" />
</head>

<body>
    <?php
        include("header.inc");
    ?>

    <div id="main">
    <div class="shell">

    <h1>Products</h1><br />

    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST" && is_numeric($_POST['qty_req']))
        {
            $qty_req = $market_db_conn->real_escape_string($_POST['qty_req']);

            if ($qty_req > $_POST['qty_avail'])
            {
                $alert = "Requested quantity of item \"{$_POST['prod_name']}\" is greater than the current quantity on hand";
                echo "<script type='text/javascript'>alert('$alert');</script>";
            }
            else
            {
                $price = $user_db_conn->real_escape_string($_POST['prod_price']);
                $total_cost = $price * $qty_req;

                $results = $user_db_conn->query("select * from vouchers limit {$total_cost}");

                $voucher_ids = [];
                if ($results->num_rows == $total_cost)
                {
                    while ($voucher = $results->fetch_array())
                    {
                        $voucher_ids[] = $voucher['voucher_id'];
                    }

                    $log_output = "<pre>Vouchers being spent:\n";
                    $log_output .= implode("\n", $voucher_ids);
                    $log_output .= "\n\n</pre>";

                    $verify_result = shell_exec("java -jar VerifyVouchers.jar " . implode(" ", $voucher_ids));
                    
                    if ($verify_result == "false")
                    {
                        $log_output .= "<pre>Error: One or more vouchers were not valid!.</pre>";
                    }
                    else
                    {
                        $log_output .= "<pre>All vouchers verified.</pre>";

                        $deposit_result = shell_exec("java -jar DepositVouchers.jar " . $VENDOR_BANK_ACC_NO . " " . implode(" ", $voucher_ids));

                        if ($deposit_result == "false")
                        {
                            $log_output .= "<pre>Error: Unable to deposit vouchers into vendor's bank account.</pre>";
                        }
                        else
                        {
                            $log_output .= "<pre>All vouchers successfully deposited into vendor's bank account.</pre>";

                            $prod_id = $market_db_conn->real_escape_string($_POST['prod_id']);
                            $query = "UPDATE products SET qty=qty-$qty_req WHERE prod_id=$prod_id";
                            $market_db_conn->query($query);
                        }
                    }
                }
                else
                {
                    $error = "Insufficient vouchers to purchase requested item.";
                }
            }
        }

        $query = "select * from products";
        $results = $market_db_conn->query($query);
    	
        $count = 0;
        echo "<table>";
        echo "<tr>";
        while($product = mysqli_fetch_array($results))
        {
            echo "<td>";
            echo "<form method='post' action=''>";
            echo "<h2>{$product['prod_name']}</h2>";
            echo "<p><b>Price:</b> \${$product['prod_price']}</p>";
            echo "<p><b>Quantity:</b> {$product['qty']}<br/></p>";
            echo "<p>{$product['description']}</p>";
            echo "<input type='hidden' name='prod_id' value='{$product["prod_id"]}' />";
            echo "<input type='hidden' name='prod_name' value='{$product["prod_name"]}' />";
            echo "<input type='hidden' name='prod_price' value='{$product["prod_price"]}' />";
            echo "<input type='hidden' name='qty_avail' value='{$product["qty"]}' />";
            echo "<input type='number' name='qty_req' min='1' max='{$product["qty"]}' /> ";
            echo "<input type='submit' name='purchase' value='Purchase' />";
            echo "</form>";
            echo "</td>";

            $count++;
            if ($count >= 5)
            {
                echo "</tr><tr>";
                $count = 0;
            }
        }
        if ($count != 0)
        {
            echo "</tr>";
        }
        echo "</table>";

        if (isset($error))
        {
            echo "<br />";
            echo "<span class='error'>{$error}</span>";
        }
        if (isset($log_output))
        {
            echo "<br />";
            echo "<h3>Voucher Spending log:</h3>";
            echo $log_output;
        }
    ?>

    </div>
    </div>

    <?php 
        include("footer.inc");
    ?>
</body>

</html>
