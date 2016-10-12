<?php 
    include("header.inc");
    include("dbconfigshop.php");
    session_start();
?>

<h1>Vouchers</h1>

 <?php
    $mysqli = new mysqli("localhost", "root", "", "user");
    $result = $mysqli->query("SELECT * FROM vouchers");
    $rows = $result->num_rows;

    echo "<br><p>You have " . $rows . " active voucher/s.</p>";

  ?> 


<?php 
    include("footer.inc");
 ?>