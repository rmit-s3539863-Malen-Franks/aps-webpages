<?php 
    include("header.inc");
    include("dbconfigshop.php");
    session_start();
?>

<h1>Spent Vouchers list</h1><br>

    <?php
  
    $db = mysqli_connect("localhost", "root","", "voucher")  or die(mysqli_error($db));
    $q = "SELECT * FROM spent_vouchers";
    $results = mysqli_query($db, $q) or die(mysqli_error($db));
    
    while($row=mysqli_fetch_array($results))
            {
                    print "<p>Voucher ID: {$row['voucher_id']}</p>";
            }
    ?> 



<?php 
    include("footer.inc");
 ?>