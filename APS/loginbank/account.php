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
	
    $db = mysqli_connect("localhost", "root","", "bank")  or die(mysqli_error($db));
    $q = "select * from bank_customers where acc_no='{$_SESSION['login_user_bank']}'";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
    
    while($row=mysqli_fetch_array($results))
            {
                    print "<h2>Welcome {$row['name']}</h2>";
                    print "<b>Account Number:</b> {$row['acc_no']}<br>";
                    print "<b>Balance:</b> &#36;{$row['balance']}<br>";
                    print "<b>Name:</b> {$row['name']}<br><br>"; 

                }
?> 

<form action="logout.php">
    <input type="submit" value="Logout" />
</form>

</div>


<?php 
    include("footer.inc");
 ?>