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

<?php
    $db = mysqli_connect("localhost", "root","", "marketplace")  or die(mysqli_error($db));
    $q = "select * from products";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
    
    while($row=mysqli_fetch_array($results))
            {
                    print "<h4>Product Name: {$row['prod_name']}</h4>\n";
                    print "<h4>Price: &#36;{$row['prod_price']}</h4>\n";
                     print "<h4>Description: {$row['description']}</h4>\n";
                    print "<h4>Quantity: {$row['qty']}</h4>\n";

             }
?>

 <div id="main-content">
    <form method="post" action="">
        <input type="submit" name="spend_vouchers" value="Submit"/>
    </form>
  </div> 
<?php /*Purchasing Button*/
if(isset($_POST['spend_vouchers'])){
	$mysqli = new mysqli("localhost", "root", "", "user");
	$result = $mysqli->query("SELECT * FROM vouchers");
    /* determine number of rows result set */
	$num = $result->num_rows;
	if($num!=null){
		/*get the price*/
		$db = mysqli_connect("localhost", "root","", "marketplace")  or die(mysqli_error($db));
		$q = "select * from products";
		$results = mysqli_query($db, $q) or die(mysqli_error($db));
		$arg ="";
		while($row=mysqli_fetch_array($results))
            {
				/*echo $row['prod_price'];*/
			/*if we have multiple Item, need to write a different query*/
               if($row['prod_price']>$num){
				   echo "not enough funds!!!";
			   }
			   else{
					while($each = mysqli_fetch_array($result)){
						/*echo "{$each['voucher_id']}";
						echo "|";*/
						$arg .= "{$each['voucher_id']} ";
					}
			   }
            }
		echo "checking argument {$arg}<br>";
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
		echo $output;
		echo "</pre>";
						   
	}
	if($num == null){
		echo "NO Voucher Found!!!</br>";
	}
    /* close result set */
    $result->close();
}
?>

</body>
</html>

<?php 
    include("footer.inc");
 ?>