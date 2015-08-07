<?php

require_once("php/security.php");
require_once("php/database.php");
require_once("php/storedprocedures.php");
session_start();

$username = $_POST["username"];

$db = connectToDatabase();
$salt = getSalt($db, $username)["salt"];

echo "Salt is: $salt";

if($salt){
	$password = $_POST["password"];
	$hash = hashPasswordCustomSalt($password, $salt);

	$results = login($db, $username, $hash);
	$loginToken = $results['token'];
	$errorMessage = $results['error'];

	if($token){
		$_SESSION['token'] = $loginToken;
	}
	else{
		// Username not found OR account is already logged in.
		header("Location: profile.php");
	}
}
else{
	// Salt not found; incorrect username
	header("Location: login.php");
}