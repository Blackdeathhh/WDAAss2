<?php

require_once("php/security.php");
require_once("php/database.php");
require_once("php/storedprocedures.php");

$username = $_POST["username"];

$db = connectToDatabase();

$salt = getSalt($db, $username);

echo "Salt is: $salt";

if($salt){
	$password = $_POST["password"];
	$hash = hashPasswordCustomSalt($password, $salt);
	$loginToken;
	$errorMessage = "";

	$stmt = $db->prepare("CALL LoginUser(:user, :hash, :token, :error)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	$stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	$stmt->bindParam(":errror", $errorMessage, PDO::PARAM_STR);
	try{
		$stmt->execute();
		$success = true;
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		echo "Something went wrong with LoginUser";
	}
	$stmt->closeCursor();
	echo "Provided $username and $hash.<br />";
	echo "Received $loginToken as token and $errorMessage as response.";
}
else
{
	echo "No salt found; incorrect username";
}