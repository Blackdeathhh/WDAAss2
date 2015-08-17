<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/friends.css" />
	<meta charset="UTF-8">
	<title>Friends List</title>
</head>
<body>
<?php require("php/topbar.php"); ?>

<div class="maindiv">
<?php
	session_start();
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");

	$db = connectToDatabase();
	if($db){
		if($_POST['addfriendid']){
			$results = addFriend($db, $_SESSION['id'], $_POST['addfriendid'], $_SESSION['token']);
			switch($results[SP::ERROR]){
				case ERR::OK:
					echo "<p>Friend added! <a href='friendslist.php'>See friends</a></p>";
					break;
				default:
					echo "<p>Could not add friend. Error: ". $ERRORS[$results[SP::ERROR]] ."</p>";
					break;
			}
		}
		else{
			$results = getFriends($db, $_SESSION['id'], $_SESSION['token']);
		}
	}
	else{
		echo "<p>Could not connect to database, please try again later.</p>";
	}
?>
</div>
</body>
</html>