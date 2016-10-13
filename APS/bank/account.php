<?php
    session_start();
    require_once("db_config.php");

    // Redirect to login page if not logged in
    if(!isset($_SESSION['login_user_bank']))
    {
       header("Location: login.php");
    }
?>
<!DOCTYPE html>

<html>

<head>
    <title>Account Details :: Public Bank</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/bank.css" />
</head>

<body>
    <?php
        include("header_nav.inc");
    ?>

    <div id="contentwrap">
    <div id="main-content">

    <?php
        $query = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
        $results = $bank_db_conn->query($query);
        $account = $results->fetch_array();
        
        echo "<h2>Welcome {$account['name']}</h2>";
        echo "<b>Account Number:</b> {$account['acc_no']}<br>";
        echo "<b>Balance:</b> &#36;{$account['balance']}<br>";
        echo "<b>Name:</b> {$account['name']}<br><br>";
    ?> 

    <form action="logout.php">
        <input type="submit" value="Logout" />
    </form>

    </div>
    </div>

    <?php 
        include("footer.inc");
     ?>
</body>

</html>
