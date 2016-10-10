<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="publicbank.css" />
<style>
	.card-container{
		perspective: 700;
	}
	.card{
		width:240px; height:120px; background:#5677fc;margin:20px;
		transition: all 0.6s ease;
		transform-style: preserve-3d;
	}
	.card:hover{
		transform: rotateY(90deg);
	}
</style>
</head>
<body>
<div class="card-container">
	<div class="card">
		<img class="image" src="bank-logo.png" alt="Public Bank Logo" height="128" width="283">
	</div>
</div>

<div id="section">
<div id="banner">
<div id="globalnav">
<ul>
<li><a href="bankindex.php">Home</a></li> 
<li><a href="voucher.php">Vouchers</a></li>
<li><a href="account.php">Account</a></li>
<li><a href="login.php">Login</a></li>
<li style="float:right"><a href="/aps-webpages/aps/index.php">Change Destination</a></li> 
</ul>
</div>


<marquee id="marq" scrollamount="3" direction="right" loop="50" scrolldelay="0" behavior="scroll" > 
	<h2 style="color:green;">Public Bank</h2> 
</marquee> 
</div>
</div>
<!-- This is for logo-->


<!-- end of header.inc -->

