<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");
//echo "ayyy you submitted this stuff...";

// Strip the message of any bad tags
// then just call the procedure

if(isset($_POST['touserid']){
	$db = connectToDatabase();
	if($db){//$database, $userID, $recipientID, $content, &$loginToken
		$results = createMessage($db, $_SESSION['id'], $_POST['touserid'], $_POST['content'], $_SESSION['token']);
		
		switch($results[SP::ERROR]){
			case ERR::OK:
				echo "<p>Message sent!</p>";
				break;
			default:
				echo "<p>Message not sent, error code: ". $results[SP::ERROR] ."</p>";
				break;
		}
	}
}