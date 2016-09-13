<?php 
include("header.inc");
include("dbconfigshop.php");
session_start();
if(!isset($_SESSION['login_user_shop']))
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
	
    $db = mysqli_connect("localhost", "root","", "website")  or die(mysqli_error($db));
    $q = "select * from website_customers where user_id='{$_SESSION['login_user_shop']}'";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
    
    while($row=mysqli_fetch_array($results))
            {
                    print "<h1>Welcome {$row['user_id']}</h1>\n";
                    print "<div>\n";
             }
    ?> 

<a href="logout.php">Logout</a>
</div>
</body>

</html>

<?php 
    include("footer.inc");
 ?>