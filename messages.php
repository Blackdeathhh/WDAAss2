<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/messages.css" />
	<meta charset="UTF-8">
	<title>Messages</title>
</head>
<body>
<?php require("php/topbar.php"); ?>

<div class="maindiv">
<?php
/*
How to do this. This could work like...
A whole bunch of user avatars or something (like friendslist), and you click on one. In fact, we could even just add a button to friendslist to view all messages. Yeah, that's a good idea. You can view your own messages if you come to this page without an ID or with your ID set to your own.
	That takes you to a page with the GET param set to find messages FROM that user only
	And show their content, CreatedAt, and so on.
*/
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

$db = connectToDatabase();
if($db){
	if(isset($_GET['userid'])){
		$userInfo = getPublicUserDetails($db, $_GET['userid']);
		$us = $_SESSION['id'];
		$them = $_GET['userid'];
		// We want to get messages that have been SENT by US and RECEIVED by THEM...
		$sentMessages = getMessages($db, $us, $us, $them, $_SESSION['token']);
		// ...and messages that have been SENT by THEM and RECEIVED by US
		$receivedMessages = getMessages($db, $us, $them, $us, $_SESSION['token']);
		$sentError = $sentMessages[SP::ERROR];
		$receivedError = $receivedMessages[SP::ERROR];
		unset($sentMessages[SP::ERROR]);
		unset($receivedMessages[SP::ERROR]);

		//MessageID, FromUserID, ToUserID, Title, CreatedAt
		switch($sentError){
			case ERR::OK:
				echo <<<EOT
	<div class='messagesbox'>
		<h2 class='title'>Messages sent to <a href='profile.php?profileid={$_GET['userid']}'>{$userInfo[USER::DISP_NAME]}</a></h2>
		<ol>
EOT;
				foreach($sentMessages as $msg => $details){
					echo <<<EOT
			<li>
				<div class='message'>
					<p><a href='viewmessage.php?messageid={$details[MESSAGE::ID]}'>{$details[MESSAGE::TITLE]}</a> at {$details[MESSAGE::MADE_AT]}</p>
				</div>
			</li>
EOT;
				}
				echo "</ol></div>";
				break;
			default:
				echo "<p>Error: ". $ERRORS[$sentError] ."</p>";
				break;
		}
		
		switch($receivedError){
			case ERR::OK:
				echo <<<EOT
	<div class='messagesbox'>
		<h2 class='title'>Messages received from <a href='profile.php?profileid={$_GET['userid']}'>{$userInfo[USER::DISP_NAME]}</a></h2>
		<ol>
EOT;
				foreach($receivedMessages as $msg => $details){
					echo <<<EOT
			<li>
				<div class='message'>
					<p><a href='{$details[MESSAGE::ID]}'>{$details[MESSAGE::TITLE]}</a> at {$details[MESSAGE::MADE_AT]}</p>
				</div>
			</li>
EOT;
				}
				echo "</ol></div>";
				break;
			default:
				echo "<p>Error: ". $ERRORS[$sentError] ."</p>";
				break;
		}
	}
	else{
		$userInfo = array();
		// We want to get messages that have been SENT by US and RECEIVED by ANYONE...
		$sentMessages = getMessages($db, $_SESSION['id'], $_SESSION['id'], null, $_SESSION['token']);
		// ...and messages that have been SENT by ANYONE and RECEIVED by US
		$receivedMessages = getMessages($db, $_SESSION['id'], null, $_SESSION['id'], $_SESSION['token']);
		$sentError = $sentMessages[SP::ERROR];
		$receivedError = $receivedMessages[SP::ERROR];
		unset($sentMessages[SP::ERROR]);
		unset($receivedMessages[SP::ERROR]);
/*
	const ID = "MessageID";
	const SENDER = "FromUserID";
	const RECIPIENT = "ToUserID";
	const CONTENT = "Content";
	const MADE_AT = "CreatedAt";
	const TITLE = "Title";
*/

		//MessageID, FromUserID, ToUserID, Title, CreatedAt
		switch($sentError){
			case ERR::OK:
				echo <<<EOT
	<div class='messagesbox'>
		<h2 class='title'>All messages sent</h2>
		<ol>
EOT;
				foreach($sentMessages as $msg => $details){
					if(isset($userInfo[$details[MESSAGE::RECIPIENT]])){
						$userInfo[$details[MESSAGE::RECIPIENT]] = getPublicUserDetails($db, $details[MESSAGE::RECIPIENT]);
					}
					echo <<<EOT
			<li>
				<div class='message'>
					<p><a href='{$details[MESSAGE::ID]}'>{$details[MESSAGE::TITLE]}</a> at {$details[MESSAGE::MADE_AT]}, sent to <a href='profile.php?profileid={$userInfo[$details[MESSAGE::RECIPIENT]]}'>{$userInfo[$details[MESSAGE::RECIPIENT]][USER::DISP_NAME]}</a></p>
				</div>
			</li>
EOT;
				}
				echo "</ol></div>";
				break;
			default:
				echo "<p>Error: ". $ERRORS[$sentError] ."</p>";
				break;
		}
		switch($receivedError){
			case ERR::OK:
				echo <<<EOT
	<div class='messagesbox'>
		<h2 class='title'>All messages received</h2>
		<ol>
EOT;
				foreach($receivedMessages as $msg => $details){
					if(isset($userInfo[$details[MESSAGE::SENDER]])){
						$userInfo[$details[MESSAGE::SENDER]] = getPublicUserDetails($db, $details[MESSAGE::SENDER]);
					}
					echo <<<EOT
			<li>
				<div class='message'>
					<p><a href='{$details[MESSAGE::ID]}'>{$details[MESSAGE::TITLE]}</a> at {$details[MESSAGE::MADE_AT]}, sent to <a href='profile.php?profileid={$userInfo[$details[MESSAGE::SENDER]]}'>{$userInfo[$details[MESSAGE::SENDER]][USER::DISP_NAME]}</a></p>
				</div>
			</li>
EOT;
				}
				echo "</ol></div>";
				break;
			default:
				echo "<p>Error: ". $ERRORS[$sentError] ."</p>";
				break;
		}
		//echo "<p>No user specified. <a href='friendslist.php'>Back to friends list</a>.</p>";
	}
}
else{
	echo "<p>Could not connect to database, please try again later.</p>";
}
?>
</div>
</body>
</html>