<?php

require_once("php/security.php");
require_once("php/database.php");
require_once("php/storedprocedures.php");

$username = $_POST["username"];

$db = connectToDatabase();

$salt = getSalt($db, $username)["salt"];

echo "Salt is: $salt";

if($salt){
	$password = $_POST["password"];
	$hash = hashPasswordCustomSalt($password, $salt);

	$results = login($db, $username, $hash);

	echo "Provided $username and $hash. ";
	echo "Received $loginToken as token and $errorMessage as response.";
}
else
{
	echo "No salt found; incorrect username";
}