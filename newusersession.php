<?php
session_start();
require_once("php/security.php");
require_once("php/database.php");
require_once("php/error.php");
require_once("php/storedprocedures.php");
require_once("php/validation.php");

if($_SESSION['token']) {
	// Already logged in
	header("Location: profile.php");
	exit;
}

$username = $_POST['username'];

$db = connectToDatabase();
$results = getSalt($db, $username);

switch($results[SP::ERROR]){
	case ERR::OK:
		$password = $_POST['password'];
		$hash = hashPasswordCustomSalt($password, $salt);

		$results = login($db, $username, $hash);
		$loginToken = $results[SP::TOKEN];
		$errorCode = $results[SP::ERROR];

		switch($results[SP::ERROR]){
			case ERR::OK:
				$results = getUserID($db, $username);
				switch($results[SP::ERROR]){
					case ERR::OK:
						$_SESSION['token'] = $loginToken;
						$_SESSION['id'] = $results[USER::ID];
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