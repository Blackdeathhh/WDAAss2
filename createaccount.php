<?php
session_start();
require_once("php/database.php");
require_once("php/validation.php");
require_once("php/security.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

$db = connectToDatabase();

if($db) {
	$username = $_POST["username"];
	$displayName = $_POST["displayname"];
	$rawPassword = $_POST["password"];
	$usernameValid = validateUsername($username);
	$displaynameValid = validateDisplayname($displayName);
	$passwordValid = validatePassword($rawPassword);

	if($usernameValid && $displaynameValid && $passwordValid){
		$hashedPass = hashPassword($rawPassword);
		$salt = substr($hashedPass, 7, 22);
		$results = registerUser($db, $username, $hashedPass, $salt, $displayName);

		switch($results[SP::ERROR]){
			case ERR::OK:
				// It worked, try to log in.
				$results = login($db, $username, $hashedPass);
				switch($results[SP::ERROR]){
					case ERR::OK:
						$_SESSION['token'] = $results[SP::TOKEN];
						$_SESSION['id'] = $results[USER::ID];
						$_SESSION['permission'] = $results[PERMISSION::LEVEL];
						// Give them a default avatar
						copy("avatar/default.jpg", "avatar/". $results[USER::ID] .".jpg");
						header("Location: profile.php");
						break;
					default:
						// Should not happen; login should only fail if authentication fails, or someone's already using that account.
						echo "Woah...something unexpected went wrong. Try to <a href='login.php'>log in manually</a>; your account was created successfully, but you were not able to be logged in. Please contact me about this, and quote this error code: ". $results[SP::ERROR];
						break;
				}
				break;
			case ERR::USERNAME_TAKEN:
				header("Location: register.php?username=". ERR::USERNAME_TAKEN);
				break;
			case ERR::DISPNAME_TAKEN:
				header("Location: register.php?displayname=". ERR::DISPNAME_TAKEN);
				break;
			default:
				header("Location: register.php?error=". $results[SP::ERROR]);
				break;
		}
	}
	else{
		$errors = array();
		if(!$usernameValid) $errors[] = "username=" . ERR::USERNAME_BAD;
		if(!$displaynameValid) $errors[] = "displayname=" . ERR::DISPNAME_BAD;
		if(!$passwordValid) $errors[] = "password=" . ERR::PASSWORD_BAD;
		header("Location: register.php?". implode("&", $errors));
	}
}
else {
	header("Location: register.php?error=". ERR::CONNECT);
}