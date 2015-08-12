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
	$results = array("Salt" => $stmt->fetchAll()[0]["Salt"]);
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
	$results = array("Token" => $sel[0]['@token'], "Error" => $sel[0]['@error']);
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
	$results = array("ID" => $stmt->fetchAll()[0]["UserID"]);
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
	$stmt->bindParam(":display", $displayName, PDO::PARAM_STR);
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
	$results = array("Error" => $errorCode);
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
	$results = array("Token" => $sel[0]['@newToken'], "Error" => $errorCode);
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
	$results = $out[0];
	$results['Error'] = $errorCode;
	//$results = array("DisplayName" => $out[0]['DisplayName'], "Location" => $out[0]['Location'], "Gender" => $out[0]['Gender'], "Error" => $errorCode);
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
		// 2053 == No rows. If no rows, we can ignore it; just return a null array.
		// If it's something else, rethrow it.
		if($e->getCode() == 2053) $out = array();
		else throw $e;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = $out[0];
	$results['Token'] = $sel[0]['@newToken'];
	$results['Error'] = $errorCode;
	// This stored procedure should only return 1 row, so just take that.
	//$results = array("displayName" => $out[0]['DisplayName'], "location" => $out[0]['Location'], "gender" => $out[0]['Gender'], "email" => $out[0]['Email'], "postsPerPage" => $out[0]['PostsPerPage'], "token" => $sel[0]['@newToken'], "error" => $errorCode);
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
	$results = array("Token" => $sel[0]['@newToken'], "Error" => $errorCode);
	return $results;
}

function getForumInfo($database, $targetForumID){
	$errorCode;
	$stmt = $database->prepare("CALL GetForumInfo(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	// Only returns a single row
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results = $out[0];
	$reuslts['Error'] = $errorCode;
	return $results;
}

function getChildForums($database, $targetForumID){
	$errorCode;
	$stmt = $database->prepare("CALL GetChildForums(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	// This stored procedure returns multiple rows, so we can just add in the error code to the array returned by SQL.
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$out['Error'] = $errorCode;
	$stmt->closeCursor();
	return $out;
	/*$results = array();
	foreach($out as $row){
		$r;
		foreach($row as $col => $val){
			$r = array();
		}
		$out[] = $r;
	}*/
}

function getForumThreads($database, $targetForumID){
	$errorCode;
	$stmt = $database->prepare("CALL GetForumThreads(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = 1;
	}
	// This stored procedure returns multiple rows, so we can just add in the error code to the array returned by SQL.
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$out['Error'] = $errorCode;
	$stmt->closeCursor();
	return $out;
}