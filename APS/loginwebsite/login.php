<?php 
include("header.inc");
include("dbconfigshop.php");
session_start();
$error = "";

if(isset($_SESSION['login_user_shop']))
    {
    header("Location: account.php");
    }


if($_SERVER["REQUEST_METHOD"] == "POST")
 {

$user_id=mysqli_real_escape_string($dbconfigshop,$_POST['user_id']);
$password=mysqli_real_escape_string($dbconfigshop,$_POST['password']);

$sql_query="SELECT user_id FROM website_customers WHERE user_id='$user_id' and password = SHA('$password')";
$result=mysqli_query($dbconfigshop,$sql_query);
$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
$count=mysqli_num_rows($result);


if($count==1)
{
$_SESSION['login_user_shop']=$user_id;

header("location: account.php");
}
else
{
$error="Username or Password is invalid";
}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Log in to Shop</title>
</head>

<body>

<div class="login-block">
    <h1>Login</h1>
    <form method="post" action="" name="loginform">
    <input type="text" placeholder="Username" id="user_id" name="user_id" />
    <input type="password" placeholder="Password" id="password" name="password" />
    <button type="submit">Submit</button>
    <span><?php echo $error; ?></span>
    </form>
</div>
</body>

</html>

<?php 
    include("footer.inc");
 ?>