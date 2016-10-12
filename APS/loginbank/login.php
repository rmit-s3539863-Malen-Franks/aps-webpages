<?php 
include("header.inc");
include("dbconfigbank.php");
session_start();
$error = "";

if(isset($_SESSION['login_user_bank']))
    {
    header("Location: account.php");
    }


if($_SERVER["REQUEST_METHOD"] == "POST")
 {
 
$acc_no=mysqli_real_escape_string($dbconfigbank,$_POST['acc_no']);
$password=mysqli_real_escape_string($dbconfigbank,$_POST['password']);

$sql_query="SELECT acc_no FROM bank_customers WHERE acc_no='$acc_no' and password = SHA('$password')";
$result=mysqli_query($dbconfigbank,$sql_query);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
$count=mysqli_num_rows($result);


if($count==1)
{
$_SESSION['login_user_bank']=$acc_no;

header("location: account.php");
}
else
{
$error = "Username or Password is invalid";
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Log in to Public Bank</title>
</head>

<body>


<h2>Login</h2>
    <form method="post" action="" name="loginform">
    <h4>Enter your login details<h4>
    <input type="text" placeholder="Account Number" id="acc_no" name="acc_no" /><br>
    <input type="password" placeholder="Password" id="password" name="password" /><br><br>
    <button type="submit">Login</button>
    <span><?php echo $error; ?></span>
    </form>



<?php 
    include("footer.inc");
 ?>