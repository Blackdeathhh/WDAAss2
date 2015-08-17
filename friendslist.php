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
		elseif($_POST['deletefriendid']){
			$results = removeFriend($db, $_SESSION['id'], $_POST['deletefriendid'], $_SESSION['token']);
			switch($results[SP::ERROR]){
				case ERR::OK:
					echo "<p>Friend removed! <a href='friendslist.php'>See friends</a></p>";
					break;
				default:
					echo "<p>Could not remove friend. Error: ". $ERRORS[$results[SP::ERROR]] ."</p>";
					break;
			}
		}
		else{
			$results = getFriends($db, $_SESSION['id'], $_SESSION['token']);
			$errorCode = $results[SP::ERROR];
			unset($results[SP::ERROR]);

			if(count($results) == 0){
				echo "<p>You have no friends ;_;</p>";
			}

			foreach($results as $num => $friend){
				$friendInfo = getPublicUserDetails($db, $friend[FRIEND::FRIEND_ID]);
				echo <<<EOT
	<div class="friendbox">
		<a href="profile.php?profileid={$friend[FRIEND::FRIEND_ID]}">
			<img class="avatar" src="avatar/{$friend[FRIEND::FRIEND_ID]}.jpg" />
		{$friendInfo[USER::DISP_NAME]}</a>
		<form method="GET" action="makemessage.php">
			<input type="hidden" name="touserid" value="{$friend[FRIEND::FRIEND_ID]}"/>
			<input type="submit" value="Send Message" />
		</form>
		<form method="POST" action="friendslist.php">
			<input type="hidden" name="deletefriendid" value="{$friend[FRIEND::FRIEND_ID]}"/>
			<input type="submit" value="Remove Friend" />
		</form>
	</div>
EOT;
			}
		}
	}
	else{
		echo "<p>Could not connect to database, please try again later.</p>";
	}
?>
</div>
</body>
</html>