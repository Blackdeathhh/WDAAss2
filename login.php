<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/login.css" />
	<script type="text/javascript" src="js/registerValidate.js"></script>
	<meta charset="UTF-8">
	<title>Log In</title>
</head>
<body>
<?php require("php/topbar.php"); ?>

<div class="maindiv">
<?php
	session_start();
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");

	$loggedIn = false;

	if(isset($_SESSION['id']) && isset($_SESSION['token'])){
		$db = connectToDatabase();
		if($db){
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
	}
	if(!$loggedIn){
		echo <<<EOT
	<div class="loginform">
		<form method="POST" action="newusersession.php">
			<ol class="logindetails">
				<li>
					<label class="loginlabels">Username: </label>
					<input type="text" id="username" name="username" />
					<label id="usernameerror"
EOT;
		if(isset($_GET['username'])){
			echo " class='errorshow'>";
			switch($_GET['username']){
				case ERR::USERNAME_BAD:
					echo "Username can be at most 20 characters with no spaces";
					break;
				default:
					echo "Username is invalid";
					break;
			}
		}
		else{
			echo " class='errorhide'>";
		}
		echo <<<EOT
					</label>
				</li>
				<li>
					<label class="loginlabels">Password: </label>
					<input type="password" id="password" name="password" />
					<label id="passworderror"
EOT;
		if(isset($_GET['password'])){
			echo " class='errorshow'>";
			switch($_GET['password']){
				case ERR::PASSWORD_BAD:
					echo "Password can be at most 72 characters with no spaces";
					break;
				default:
					echo "Password is invalid";
					break;
			}
		}
		else{
			echo " class='errorhide'>";
		}
		echo <<<EOT
					</label>
				</li>
				<li>
					<label id='errormessage'>
EOT;
		if(isset($_GET['error'])){
			switch($_GET['error']){
				default:
					echo "Something went wrong";
					break;
			}
		}
		else{
		
		}
		echo <<<EOT
					</label>
				</li>
				<li>
					<input type="submit" id="submit" value="Log In" />
				</li>
			</ol>
		</form>
	</div>
EOT;
	}
	else{
		echo "<p>You are already logged in. You can see your <a href='profile.php'>profile here</a>.</p>";
	}
?>
</div>
</body>
</html>