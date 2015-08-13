<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/login.css" />
	<script type="text/javascript" src="js/registerValidate.js"></script>
	<meta charset="UTF-8">
</head>
<body>
<?php require("php/topbar.php"); ?>

<div class="maindiv">
	<div class="loginform">
		<form method="POST" action="createaccount.php">
			<ol class="logindetails">
				<li><label class="loginlabels">Username: </label><input type="text" id="username" name="username" oninput="validate()"></input><label id="usernameerror" class="errorhide"></label></li>
				<li><label class="loginlabels">Display Name: </label><input type="text" id="displayname" name="displayname" oninput="validate()"></input><label id="displaynameerror" class="errorhide"></label></li>
				<li><label class="loginlabels">Password: </label><input type="password" id="password" name="password" oninput="validate()"></input><label id="passworderror" class="errorhide"></label></li>
				<li><label id="errormessage"></label></li>
				<li><input type="submit" id="submit" value="Register" disabled /></li>
			</ol>
		</form>
	</div>
</div>
</body>
</html>