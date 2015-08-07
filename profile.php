<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
</head>
<body>
<div id="topbar">
	<img src="img/header.png" />
</div>

<div class="maindiv">
	<ol>
<?php
	session_start();

	echo "<li><label>Current Token: </label>" . $_SESSION['token'] . "</li>";
	echo "<li><label>Display Name: </label></li>";
	echo "<li><label>Location: </label></li>";
	echo "<li><label>Email: </label></li>";
	echo "<li><label>Gender: </label></li>";
	echo "<li><label>Posts per Page: </label></li>";
?>
	</ol>
</div>
</body>
</html>