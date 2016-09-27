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

</body>
</html>

<?php 
    include("footer.inc");
 ?>