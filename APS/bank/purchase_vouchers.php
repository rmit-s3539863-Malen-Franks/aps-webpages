<?php 
    session_start();
    require_once("db_config.php");

    // Redirect to login page if not logged in
    if(!isset($_SESSION['login_user_bank']))
    {
       header("Location: login.php");
    }

    set_time_limit(600);
?>
<!DOCTYPE html>

<html>

<head>
    <title>Purchase Vouchers :: Public Bank</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/bank.css" />
</head>

<body>
    <?php
        include("header_nav.inc");
    ?>

    <div id="contentwrap">
    <div id="main-content">

    <h2>Purchase Vouchers</h2>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST")
        {
            $generate_log = shell_exec("java -jar GenerateVouchers.jar {$_SESSION['login_user_bank']} {$_POST['num_vouchers']}");

            $query = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
            $results = $bank_db_conn->query($query);
            $account = $results->fetch_array();
            
            echo "<h3>You purchased {$_POST['num_vouchers']} voucher(s). Your remaining balance is now \${$account['balance']}</h3>";
        }
        else
        {
            $query = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
            $results = $bank_db_conn->query($query);
            $account = $results->fetch_array();

            echo "<h3>Your current balance is \${$account['balance']}</h3>";
        }
    ?>

    <b>Enter number of vouchers</b><br>
    <form method="post" action="">
        <input type="number" name="num_vouchers" />
        <input type="submit" value="Purchase" />
    </form>

    <?php
        if (isset($generate_log))
        {
            echo "<h3>Voucher Generation log:</h3>";
            echo "<pre>{$generate_log}</pre>";
        }
    ?>
 
    </div>
    </div>

    <?php 
        include("footer.inc");
    ?>
</body>

</html>
