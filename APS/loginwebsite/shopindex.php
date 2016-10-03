<?php 
    include("header.inc");
    include("dbconfigshop.php");
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Shop</title>
</head>
<body>


 <h1>Welcome to the Shop</h1> 

<div id="main-content">
<?php
    $db = mysqli_connect("localhost", "root","", "marketplace")  or die(mysqli_error($db));
    $q = "select * from products";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));

    while($row=mysqli_fetch_array($results))
    {
        echo '<form method="post" action="">' . "\n";
        echo "<b>Product Name:</b> {$row['prod_name']}<br/>\n";
        echo "<b>Price:</b> &#36;{$row['prod_price']}<br/>\n";
        echo "<b>Description:</b> {$row['description']}<br/>\n";
        echo "<b>Quantity:</b> {$row['qty']}<br/>\n";
        echo '<input type="hidden" name="prod_price" value="'
                . $row['prod_price'] . '" />' . "\n";
        echo '<input type="submit" name="spend_vouchers" value="Submit" />'
                . "\n";
        echo '</form>' . "\n";

    }
?>
</div> 
<?php /*Purchasing Button*/
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $price = $_POST['prod_price'];
    
    $user_db = new mysqli("localhost", "root", "", "user");

    $vouchers_result = $user_db->query("select * from vouchers limit {$price}");

    $arg = "";
    if ($vouchers_result->num_rows == $price)
    {
        while ($voucher = mysqli_fetch_array($vouchers_result))
        {
            $arg .= $voucher['voucher_id']." ";
        }
        echo "Verifying vouchers: {$arg}<br>";
        if (!file_exists("VerifyVouchers.jar"))
        {
           // Install ivy if not present
           shell_exec("ant ivy");
           // Resolve dependencies into 'lib' directory if not present
           shell_exec("ant resolve");
           // Create VerifyVouchers.jar
           shell_exec("ant VerifyVouchers-jar");
        }
        $output = shell_exec("java -jar VerifyVouchers.jar ".$arg);
        // Do whatever you need to with $output
        echo "<pre>";
        echo "Verifying Vouchers successful = " . $output;
        echo "</pre>";

        $message = "Depositing vouchers now";
        echo "<script type='text/javascript'>alert('$message');</script>";
        if (!file_exists("DepositVouchers.jar"))
        {
           // Install ivy if not present
           shell_exec("ant ivy");
           // Resolve dependencies into 'lib' directory if not present
           shell_exec("ant resolve");
           // Create DepositVouchers.jar
           shell_exec("ant DepositVouchers-jar");
        }
        $acc_no = 78910;
        $output = shell_exec("java -jar DepositVouchers.jar " . $acc_no . " " .$arg);
        echo "<pre>";
        echo "Depositing Vouchers successful = " . $output;
        echo "</pre>";
        
    /*2nd option
    if ($output == true)
                {   
                $message = "Depositing vouchers now";
                echo "<script type='text/javascript'>alert('$message');</script>";
                if (!file_exists("DepositVouchers.jar"))
                {
                   // Install ivy if not present
                   shell_exec("ant ivy");
                   // Resolve dependencies into 'lib' directory if not present
                   shell_exec("ant resolve");
                   // Create DepositVouchers.jar
                   shell_exec("ant DepositVouchers-jar");
                }
                $acc_no = 78910;
                $output = shell_exec("java -jar DepositVouchers.jar " . $acc_no . " " .$arg);
                echo "<pre>";
                echo "Depositing Vouchers successful = " . $output;
                echo "</pre>";
                }
            else
                {
                     echo "Vouchers not verified";
                }
    */

    }
    else
    {
        echo "Insufficient vouchers to purchase requested item.";
    }

    /* close result set */
    $vouchers_result->close();
    
    // TODO Now deposit these vouchers into the vendors bank account
    
}
?>

</body>
</html>

<?php 
    include("footer.inc");
 ?>