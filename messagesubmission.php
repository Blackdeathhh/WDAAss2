<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");
require_once("php/validation.php");

$msgTitle = $_POST['messagetitle'];

if(validateMessageTitle($msgTitle)){
	if(isset($_POST['touserid'])){
		$db = connectToDatabase();
		if($db){
			$results = createMessage($db, $_SESSION['id'], $_POST['touserid'], $msgTitle, $_POST['content'], $_SESSION['token']);
			
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
}
else{
	header("Location: makemessage.php?title=". ERR::MSG_TITLE_BAD);
}