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
<?php
session_start();
require_once("php/error.php");
require_once("php/storedprocedures.php");
require_once("php/database.php");

$db = connectToDatabase();

if($db){
	$loggedIn = false;
	if(isset($_SESSION['id']) && isset($_SESSION['token']))
	{
		$results = verifyUser($db, 0, $_SESSION['id'], $_SESSION['token']);
		switch($results[SP::ERROR]){
			case ERR::OK:
				//$_SESSION['token'] = $results[SP::TOKEN];
				$loggedIn = true;
				break;
			default:
				// We don't care WHAT went wrong; this just means that the user's not logged in, which is all we need to know right now.
				unset($_SESSION['token']);
				unset($_SESSION['id']);
				break;
		}
	}
	if(!$loggedIn){
		// username, displayname, password, other
		echo <<<EOT
<div class="loginform">
	<form method="POST" action="createaccount.php">
		<ol class="logindetails">
			<li>
				<label class="loginlabels">Username: </label>
				<input type="text" id="username" name="username" oninput="validate()" />
				<label id='usernameerror' class=
EOT;
		if(isset($_GET['username'])){
			echo "'errorshow'>";
			switch($_GET['username']){
				case ERR::USERNAME_TAKEN:
					echo "Username is already taken";
					break;
				case ERR::USERNAME_BAD:
					echo "Username can be at most 20 characters with no spaces";
					break;
			}
		}
		else{
			echo "'errorhide'>";
		}
		echo <<<EOT
				</label>
			</li>
			<li>
				<label class='loginlabels'>Display Name: </label>
				<input type='text' id='displayname' name='displayname' oninput='validate()' />
				<label id='displaynameerror' class=
EOT;
		if(isset($_GET['displayname'])){
			echo "'errorshow'>";
			switch($_GET['displayname']){
				case ERR::DISPNAME_TAKEN:
					echo "Display Name is already taken";
					break;
				case ERR::DISPNAME_BAD:
					echo "Display Name can be at most 20 characters with no spaces";
					break;
			}
		}
		else{
			echo "'errorhide'>";
		}

		echo <<<EOT
				</label>
			</li>
			<li>
				<label class="loginlabels">Password: </label>
				<input type="password" id="password" name="password" oninput="validate()" />
				<label id="passworderror" class=
EOT;
		if(isset($_GET['password'])){
			echo "'errorshow'>";
			switch($_GET['password']){
				case ERR::PASSWORD_BAD:
					echo "Password can be at most 72 characters with no spaces";
					break;
			}
		}
		else{
			echo "'errorhide'>";
		}
		echo "</label></li><li><label id='errormessage'>";
		if(isset($_GET['error'])){
			switch($_GET['error']){
				case ERR::CONNECT:
					echo "Could not connect to create account, please try again later.";
					break;
			}
		}
		echo <<<EOT
			</label>
		</li>
			<li>
				<input type='submit' id='submit' value='Register' disabled />
			</li>
		</ol>
	</form>
</div>
EOT;
	}
	else{
		echo "<p>You're already logged in. Maybe you want to <a href='logout.php'>log out</a>?</p>";
	}
}
else{
	echo "<p>Cannot connect to the database, please try again later.</p>";
}
?>
</div>
</body>
</html>