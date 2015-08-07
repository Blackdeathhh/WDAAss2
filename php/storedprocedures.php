<?php

function getSalt($database, $username) {
	$stmt = $database->prepare("CALL GetSalt(:user)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
	}
	// Table's just one row, one column.
	$results = array("salt" => $stmt->fetchAll()[0]["Salt"]);
	$stmt->closeCursor();
	return $results;
}

function login($database, $username, $hash) {
	$stmt = $database->prepare("CALL Login(:user, :hash, @token, @error)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	$stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
	//$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	//$stmt->bindParam(":error", $errorMessage, PDO::PARAM_STR);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		echo "Something went wrong with Login";
	}
	$sel = $database->query("SELECT @token, @error")->fetchAll();
	$results = array("token" => $sel[0]['@token'], "error" => $sel[0]['@error']);
	$stmt->closeCursor();
	return $results;
}

/*function registerUser($database, $username, $hash, $salt, $displayName) {

}*/