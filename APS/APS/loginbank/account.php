<?php 
include("header.inc");
include("dbconfigbank.php");
session_start();
if(!isset($_SESSION['login_user_bank']))
	{
	header("Location: login.php");
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>Account Details</title>
</head>

<body>

    <?php
	
    $db = mysqli_connect("localhost", "root","", "bank")  or die(mysqli_error($db));
    $q = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
    
    while($row=mysqli_fetch_array($results))
            {
                    print "<h1>Welcome {$row['name']}</h1>\n";
                    print "<div>\n";
                    print "<h4>Account Number: {$row['acc_no']}</h4>\n";
                    print "<h4>Balance: &#36;{$row['balance']}</h4>\n";
                    print "<h4>Name: {$row['name']}</h4>\n";
                    print "</div>\n"; 
                }
    ?> 

<a href="logout.php">Logout</a>
</div>
</body>

</html>

<?php 
    include("footer.inc");
 ?>