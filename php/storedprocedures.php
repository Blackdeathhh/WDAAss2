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
	$errorMessage;
	//$stmt = $database->prepare("CALL Login(:user, :hash, :token, :error)");
	$stmt = $database->prepare("CALL Login(:user, :hash, @token, @error)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	$stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
	//$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT, 11);
	//$stmt->bindParam(":error", $errorMessage, PDO::PARAM_STR, 50);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		$errorMessage = "Something went wrong with Login";
	}
	$out = $database->query("SELECT @token, @error")->fetchAll();
	$results = array("token" => $out[0]['@token'], "error" => $out[0]['@error']);
	//$results = array("token" => $loginToken, "error" => $errorMessage);
	$stmt->closeCursor();
	return $results;
}

function getUserID($database, $username) {
	$stmt = $database->prepare("CALL GetUserID(:user)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
	}
	// Table's just one row, one column
	$results = array("id" => $stmt->fetchAll()[0]["UserID"]);
	$stmt->closeCursor();
	return $results;
}

function registerUser($database, $username, $hash, $salt, $displayName) {
	$errorMessage;
	//$stmt = $db->prepare("CALL RegisterUser(:user, :pass, :salt, :display, :error)");
	$stmt = $database->prepare("CALL RegisterUser(:user, :pass, :salt, :display, @error)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	$stmt->bindParam(":pass", $hash, PDO::PARAM_STR);
	$stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
	$stmt->bindParam(":display", $displayname, PDO::PARAM_STR);
	//$stmt->bindParam(":error", $errorMessage, PDO::PARAM_STR, 50);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorMessage = "Unknown error; please try again later.";
	}
	$out = $database->query("SELECT @error")->fetchAll();
	$results = array("error" => $out[0]['@error']);
	//$results = array("error" => $errorMessage);
	$stmt->closeCursor();
	return $results;
}

function verifyAndUpdateLoginToken($database, $userID, $oldToken) {
	$errorMessage;
	$stmt = $database->prepare("CALL VerifyAndUpdateLoginToken(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $oldToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorMessage = "Unknown error; please try again later.";
	}
	$out = $database->query("SELECT @error, @newToken")->fetchAll();
	$results = array("token" => $out[0]['@newToken'], "error" => $out[0]['@error']);
	$stmt->closeCursor();
	return $results;
}

function getPublicUserDetails($database, $userID){
	$errorMessage;
	$stmt = $database->prepare("CALL GetPublicUserDetails(:id)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorMessage = "Unknown error; please try again later.";
	}
	$sel = $stmt->fetchAll();
	$results = array("displayName" => $sel[0]['DisplayName'], "location" => $sel[0]['Location'], "gender" => $sel[0]['Gender'], "error" => $errorMessage);
	$stmt->closeCursor();
	return $results;
}

function getPrivateUserDetails($database, $userID, $loginToken){
	$errorMessage;
	$stmt = $database->prepare("CALL GetPrivateUserDetails(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorMessage = "Unknown error; please try again later.";
	}
	$sel;
	try{
		$sel = $stmt->fetchAll();
	}
	catch(PDOException $e){
		// 2053 == No rows. If no rows, we can ignore it; just a null array.
		// If it's something else, rethrow it.
		if($e->getCode() == 2053) $sel = array();
		else throw $e;
	}
	$stmt->closeCursor();
	$out = $database->query("SELECT @error, @newToken")->fetchAll();
	$results = array("displayName" => $sel[0]['DisplayName'], "location" => $sel[0]['Location'], "gender" => $sel[0]['Gender'], "email" => $sel[0]['Email'], "postsPerPage" => $sel[0]['PostsPerPage'], "token" => $out[0]['@newToken'], "error" => $out[0]['@error']);
	return $results;
}