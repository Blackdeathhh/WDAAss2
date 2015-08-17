<?php
session_start();
require_once("php/security.php");
require_once("php/database.php");
require_once("php/error.php");
require_once("php/storedprocedures.php");
require_once("php/validation.php");

$db = connectToDatabase();
$loggedIn = false;

if(isset($_SESSION['token']) && isset($_SESSION['id'])) {
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
if($loggedIn){
	// Already logged in
	header("Location: profile.php");
	exit;
}

$username = $_POST['username'];

$results = getSalt($db, $username);

switch($results[SP::ERROR]){
	case ERR::OK:
		$password = $_POST['password'];
		$hash = hashPasswordCustomSalt($password, $results[LOGIN::SALT]);

		$results = login($db, $username, $hash);
		$loginToken = $results[SP::TOKEN];

		switch($results[SP::ERROR]){
			case ERR::OK:
				$results = getUserID($db, $username);
				switch($results[SP::ERROR]){
					case ERR::OK:
						$_SESSION['token'] = $loginToken;
						$_SESSION['id'] = $results[USER::ID];
						$_SESSION['permission'] = $results[PERMISSION::LEVEL];
						header("Location: profile.php");
						exit;
						break;
					default:
						header("Location: login.php?error=". $results[SP::ERROR]);
						break;
				}
				break;
			default:
				header("Location: login.php?error=". $results[SP::ERROR]);
				break;
		}
		break;
	default:
		header("Location: login.php?error=". $results[SP::ERROR]);
		break;
}