<?php 
    include("header.inc");
    include("dbconfigshop.php");
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Shop</title>
<link rel="stylesheet" href="loginCss.css" />
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lobster" />
</head>
<body>


<div align="centre"> <h1>Welcome to the Shop</h1> </div>

<div id="main-content">

<?php
    $db = mysqli_connect("localhost", "root","", "marketplace")  or die(mysqli_error($db));
    $q = "select * from products";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
	echo "<div id='items_display'>";
	while($row=mysqli_fetch_array($results))
	{
		echo"<table>";
			echo"<tr>";
				echo"<td><img src='/aps-webpages/APS/item.jpeg' style='width:304px;height:228px;'></td>";
				echo"<td>";
					echo"<table>";
						echo '<form method="post" action="">' . "\n";
						echo "<tr><td>Product Name</td><td>:\t{$row['prod_name']}</td></tr>\n";
						echo "<tr><td>Price</td><td>:\t&#36;{$row['prod_price']}</td></tr>\n";
						echo "<tr><td>Description</td><td>:\t{$row['description']}</td></tr>\n";
						echo "<tr><td>Quantity</td><td>:\t{$row['qty']}</td></tr>\n";
						echo '<input type="hidden" name="prod_price" value="'
								. $row['prod_price'] . '" />' . "\n";
						echo '<tr><td><input type="submit" name="spend_vouchers" value="Purchase" /></td></tr>'
								. "\n";
						echo '</form>' . "\n";
					echo"</table>";
				echo"</td>";
			echo"</tr>";
		echo"</table>";
	}
   echo"</div>";
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