<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/login.css" />
</head>
<body>
<div id="topbar">
	<img src="img/header.png" />
</div>

<div class="maindiv">
	<div class="loginform">
		<form method="POST" action="newusersession.php">
			<ol class="logindetails">
				<li><label class="loginlabels">Username: </label><input type="text" id="username" name="username"></input><label id="usernameerror" class="errorhide"></label></li>
				<li><label class="loginlabels">Password: </label><input type="password" id="password" name="password"></input><label id="passworderror" class="errorhide"></label></li>
				<li><label id="errormessage"></label></li>
				<li><input type="submit" id="submit" value="Log In" /></li>
			</ol>
		</form>
	</div>
</div>
</body>
</html>