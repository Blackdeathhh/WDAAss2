<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");


$db = connectToDatabase();
if($db){
	if(isset($_GET['lock'])){
		$results = setThreadLock($db, $_SESSION['id'], $_GET['lock'], 0, $_SESSION['token']);
		switch($results[SP::ERROR]){
			case ERR::OK:
				echo "<p>Thread successfully locked!</p>";
				break;
			default:
				echo "<p>Could not lock thread. Reason: ". $ERRORS[$reuslts[SP::ERROR]] ."</p>";
				break;
		}
	}
	elseif(isset($_GET['unlock'])){
		$results = setThreadLock($db, $_SESSION['id'], $_GET['lock'], 1, $_SESSION['token']);
		switch($results[SP::ERROR]){
			case ERR::OK:
				echo "<p>Thread successfully unlocked!</p>";
				break;
			default:
				echo "<p>Could not unlock thread. Reason: ". $ERRORS[$reuslts[SP::ERROR]] ."</p>";
				break;
		}
	}
	else{
		echo "<p>No thread specified.</p>";
	}
}
else{
	echo "<p>There was an error connection to the database. Please try again later.</p>";
}