<?php
    session_start();
    require_once("db_config.php");

    // Redirect to account page if logged in
    if(isset($_SESSION['login_user_bank']))
    {
        header("Location: account.php");
    }

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $acc_no = $bank_db_conn->real_escape_string($_POST['acc_no']);
        $password = $bank_db_conn->real_escape_string($_POST['password']);
        $query = "SELECT acc_no FROM bank_customers WHERE acc_no='$acc_no' and password = SHA('$password')";

        $result = $bank_db_conn->query($query);
        $count = $result->num_rows;


        if($count == 1)
        {
            $_SESSION['login_user_bank'] = $acc_no;

            header("location: account.php");
        }
        else
        {
            $error = "Username or Password is invalid";
        }
    }
?>

<!DOCTYPE html>

<html>

<head>
    <title>Login :: Public Bank</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/bank.css" />
</head>

<body>
    <?php
        include("header_nav.inc");
    ?>

    <div id="contentwrap">
    <div id="main-content">

    <h2>Login</h2>

    <h4>Enter your login details</h4>

    <form method="post" action="" name="loginform">
        <p>
            <input type="text" placeholder="Account Number" id="acc_no" name="acc_no" /><br />
            <input type="password" placeholder="Password" id="password" name="password" />
        </p>
        <p>
            <input type="submit" value="Login" />
            <?php
                if(isset($error))
                {
                    echo "<span class='error'>{$error}</span>";
                }
            ?>
        </p>
    </form>

    </div>
    </div>

    <?php 
        include("footer.inc");
     ?>
</body>

</html>
