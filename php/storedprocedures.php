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
	$errorCode;
	//$stmt = $database->prepare("CALL Login(:user, :hash, :token, :error)");
	$stmt = $database->prepare("CALL Login(:user, :hash, @token, @error)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	$stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
	//$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT, 11);
	//$stmt->bindParam(":error", $errorCode, PDO::PARAM_STR, 50);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		$errorCode = 1;
	}
	$sel = $database->query("SELECT @token, @error")->fetchAll();
	$results = array("token" => $sel[0]['@token'], "error" => $sel[0]['@error']);
	//$results = array("token" => $loginToken, "error" => $errorCode);
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
	$errorCode;
	//$stmt = $db->prepare("CALL RegisterUser(:user, :pass, :salt, :display, :error)");
	$stmt = $database->prepare("CALL RegisterUser(:user, :pass, :salt, :display, @error)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	$stmt->bindParam(":pass", $hash, PDO::PARAM_STR);
	$stmt->bindParam(":salt", $salt, PDO::PARAM_STR);
	$stmt->bindParam(":display", $displayname, PDO::PARAM_STR);
	//$stmt->bindParam(":error", $errorCode, PDO::PARAM_STR, 50);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	$sel = $database->query("SELECT @error")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function verifyAndUpdateLoginToken($database, $userID, $oldToken) {
	$errorCode;
	$stmt = $database->prepare("CALL VerifyAndUpdateLoginToken(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $oldToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("token" => $sel[0]['@newToken'], "error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function getPublicUserDetails($database, $userID){
	$errorCode;
	$stmt = $database->prepare("CALL GetPublicUserDetails(:id)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	$out = $stmt->fetchAll();
	$results = array("displayName" => $out[0]['DisplayName'], "location" => $out[0]['Location'], "gender" => $out[0]['Gender'], "error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function getPrivateUserDetails($database, $userID, $loginToken){
	$errorCode;
	$stmt = $database->prepare("CALL GetPrivateUserDetails(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	$out;
	try{
		$out = $stmt->fetchAll();
		$stmt->closeCursor();
		$stmt = NULL;
	}
	catch(PDOException $e){
		// 2053 == No rows. If no rows, we can ignore it; just a null array.
		// If it's something else, rethrow it.
		if($e->getCode() == 2053) $out = array();
		else throw $e;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("displayName" => $out[0]['DisplayName'], "location" => $out[0]['Location'], "gender" => $out[0]['Gender'], "email" => $out[0]['Email'], "postsPerPage" => $out[0]['PostsPerPage'], "token" => $sel[0]['@newToken'], "error" => $errorCode);
	return $results;
}

function modifyUserDetails($database, $userID, $loginToken, $newLocation, $newEmail, $newGender, $newPostsPerPage)
{
	$errorCode;
	$stmt = $database->prepare("CALL ModifyUserDetails(:id, :location, :email, :gender, :postsperpage, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":location", $newLocation, PDO::PARAM_STR);
	$stmt->bindParam(":email", $newEmail, PDO::PARAM_STR);
	$stmt->bindParam(":gender", $newGender, PDO::PARAM_STR);
	$stmt->bindParam(":postsperpage", $newPostsPerPage, PDO::PARAM_STR);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("token" => $sel[0]['@newToken'], "error" => $errorCod);
	return $results;
}