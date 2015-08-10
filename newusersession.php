<?php

require_once("php/security.php");
require_once("php/database.php");
require_once("php/error.php");
require_once("php/storedprocedures.php");
session_start();

if($_SESSION['token']) {
	// Already logged in
	header("Location: profile.php");
	exit;
}

$username = $_POST['username'];

$db = connectToDatabase();
$salt = getSalt($db, $username)['salt'];

if($salt){
	$password = $_POST['password'];
	$hash = hashPasswordCustomSalt($password, $salt);

	$results = login($db, $username, $hash);
	$loginToken = $results['token'];
	$errorCode = $results['error'];

	if($errorCode == ERR::OK){
		$results = getUserID($db, $username);
		$_SESSION['token'] = $loginToken;
		$_SESSION['id'] = $results['id'];
		header("Location: profile.php");
		exit;
	}
	else{
		// Username not found or someone is already logged in
		header("Location: login.php");
		exit;
	}
}
else{
	// Salt not found; incorrect username
	header("Location: login.php");
	exit;
}