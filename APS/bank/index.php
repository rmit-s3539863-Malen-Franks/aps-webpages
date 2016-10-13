<?php
    session_start();
    require_once("db_config.php");
?>
<!DOCTYPE html>

<html>

<head>
    <title>Public Bank</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/bank.css" />
</head>

<body>
    <?php
        include("header_nav.inc");
    ?>

    <div id="contentwrap">
    <div id="main-content">

        <h2>Welcome to the Public Bank</h2> 
        <p><b>From this page you can do the following:</b></p>
        <ul>
            <li>Log in to your account</li>
            <li>Check your account balance</li>
            <li>Purchase Vouchers</li>
        </ul>

    </div>
    </div>

    <?php 
        include("footer.inc");
    ?>
</body>

</html>
