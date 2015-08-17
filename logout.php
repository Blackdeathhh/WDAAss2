<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/login.css" />
	<script type="text/javascript" src="js/registerValidate.js"></script>
	<meta charset="UTF-8">
	<title>Log Out</title>
</head>
<body>
<?php require("php/topbar.php"); ?>

<div class="maindiv">
<?php
	session_start();
	require_once("php/error.php");
	require_once("php/database.php");
	require_once("php/storedprocedures.php");

	if(isset($_SESSION['token']) && isset($_SESSION['id'])){
		$db = connectToDatabase();
		if($db){
			$result = logout($db, $_SESSION['id'], $_SESSION['token']);
			if(isset($_GET['error'])){
				switch($_GET['error']){
					case ERR::USER_NO_TOKEN:
					case ERR::TOKEN_FAIL:
						echo "<p>Something went wrong with your session, please try to log in again later.</p>";
						break;
					case ERR::TOKEN_EXPIRED:
						unset($_SESSION['id']);
						unset($_SESSION['token']);
						echo "<p>Your session has expired. Please <a href='login.php'>log in</a> again.</p>";
						break;
					case ERR::UNKNOWN:
					default:
						echo "<p>You have been logged out due to an unknown error. Error code: ". $_GET['error'] ."</p>";
						break;
				}
			}
			else{
				switch($results[SP::ERROR]){
					case ERR::OK:
						unset($_SESSION['id']);
						unset($_SESSION['token']);
						echo "<p>You have been logged out.</p>";
						break;
					case ERR::USER_NOT_LOGGED:
						unset($_SESSION['id']);
						unset($_SESSION['token']);
						echo "<p>Well you weren't logged on in the first place so...I guess you're good.</p>";
						break;
					case ERR::TOKEN_FAIL:
						echo "<p>This is weird. Shouldn't happen. Tell me this error code:". $results[SP::ERROR] ."</p>";
						break;
				}
			}
		}
		else{
			echo "<p>Could not connect to the database, please try again later.</p>";
		}
	}
	else header("Location: login.php");
?>
</div>
</body>
</html>