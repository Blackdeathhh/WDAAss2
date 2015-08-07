<?php

require_once("php/security.php");
require_once("php/database.php");
require_once("php/storedprocedures.php");
session_start();

if($_SESSION['token']) {
	// Already logged in
	header("Location: profile.php");
	exit;
}

$username = $_POST["username"];

$db = connectToDatabase();
$salt = getSalt($db, $username)["salt"];

if($salt){
	$password = $_POST["password"];
	$hash = hashPasswordCustomSalt($password, $salt);

	$results = login($db, $username, $hash);
	$loginToken = $results['token'];
	$errorMessage = $results['error'];
	
	echo $loginToken . " " . $errorMessage;

	if($token){
		$_SESSION['token'] = $loginToken;
		header("Location: profile.php");
		exit;
	}
	else{
		// Username not found
		header("Location: login.php");
		exit;
	}
}
else{
	// Salt not found; incorrect username
	header("Location: login.php");
	exit;
}