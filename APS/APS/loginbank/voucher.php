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
                    print "<h4>{$row['name']}, your current balance is: &#36;{$row['balance']}</h4>";
                }
 ?> 





<!DOCTYPE html>
<html>
<head>
<title>Voucher</title>
</head>

<body>

<!-- start of form -->
 <div id="main-content">
    <form method="post" action="****ADD HERE*****" enctype="multipart/form-data">
        
        Enter Voucher amount: <input type="text" name="balance" /><br>
        <input type="submit" value="Purchase" />
    </form>
  </div>
 <!-- end of form -->

</body>
</html>


 
 <?php 
    include("footer.inc");
 ?>