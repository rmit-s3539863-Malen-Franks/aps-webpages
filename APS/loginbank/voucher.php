<!DOCTYPE html>
<html>
<head>
<title>Voucher</title>
</head>

<body>

<?php 
include("header.inc");
include("dbconfigbank.php");
session_start();
if(!isset($_SESSION['login_user_bank']))
    {
    header("Location: login.php");
    }
?>



<?php
	set_time_limit(600);
    $db = mysqli_connect("localhost", "root","", "bank")  or die(mysqli_error($db));
    $q = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
    


    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if (!file_exists("GenerateVouchers.jar"))
        {
            // Install ivy if not present
            shell_exec("ant ivy");
            // Resolve dependencies into 'lib' directory if not present
            shell_exec("ant resolve");
            // Create GenerateVouchers.jar
            shell_exec("ant GenerateVouchers-jar");
        }

        $output = shell_exec("java -jar GenerateVouchers.jar " . $_SESSION['login_user_bank']. " " . $_POST["num_vouchers"]);
        // Do whatever you need to with $output
        $db = mysqli_connect("localhost", "root","", "bank")  or die(mysqli_error($db));
        $q = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
        $results = mysqli_query($db, $q) or die(mysqli_error($db));
        
        while($row=mysqli_fetch_array($results))
        {
            print "<h4>Hi {$row['name']}, your current balance is: &#36;{$row['balance']}</h4>";
            $balance = $row['balance'];
        }
            
        echo "<pre>";
        echo $output;
        echo "</pre>";

        $message = "You purchased " . $_POST["num_vouchers"] . " voucher/s. Your remaining balance is now $" . $balance;
        echo "<script type='text/javascript'>alert('$message');</script>";
    }
    else
    {
        while($row=mysqli_fetch_array($results))
        {
            print "<h4>Hi {$row['name']}, your current balance is: &#36;{$row['balance']}</h4>";
            $balance = $row['balance'];
        }
}

?>





<!-- start of form -->
 <div id="main-content">
    <form method="post" action="">
        <input type="number" name="num_vouchers" />
        <input type="submit" value="Submit" />
    </form>
  </div>
 <!-- end of form -->

</body>
</html>


 
 <?php 
    include("footer.inc");
 ?>