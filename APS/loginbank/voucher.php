<?php 
include("header.inc");
include("dbconfigbank.php");
session_start();
if(!isset($_SESSION['login_user_bank']))
    {
    header("Location: login.php");
    }
?>



<form method="post" action="">
    <h2>Purchase Vouchers</h2>
    <b>Enter number of vouchers</b><br>
    <input type="number" name="num_vouchers" />
    <input type="submit" value="Purchase" />
</form>


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
            print "<h3>Your current balance is: &#36;{$row['balance']}</h3>";
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
            print "<h3>Your current balance is: &#36;{$row['balance']}</h3>";
            $balance = $row['balance'];
        }
}

?>
 
 <?php 
    include("footer.inc");
 ?>