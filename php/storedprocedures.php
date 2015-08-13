<?php
require_once("php/error.php");

class USER{
	const ID = "UserID";
	const DISP_NAME = "DisplayName";
	const LOC = "Location";
	const EMAIL = "Email";
	const SEX = "Gender";
	const POSTS_PAGE = "PostsPerPage";
}

class POST{
	const ID = "PostID";
	const THREAD_ID = "InThreadID";
	const USER_ID = "PostingUserID";
	const CONTENT = "Content";
	const MADE_AT = "CreatedAt";
	const EDITED_AT = "LastEdited";
	const EDITING_USER_ID = "EditedByUserID";
}

class SP{
	const ERROR = "Error";
	const TOKEN = "Token";
}

function getSalt($database, $username) {
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetSalt(:user)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		$errorCode = ERR::UNKNOWN;
	}
	// Table's just one row, one column.
	$results = array("Salt" => $stmt->fetchAll()[0]["Salt"], "Error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function login($database, $username, $hash) {
	$errorCode = ERR::OK;
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
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @token, @error")->fetchAll();
	$results = array("Token" => $sel[0]['@token'], "Error" => $sel[0]['@error']);
	//$results = array("token" => $loginToken, "error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function getUserID($database, $username) {
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetUserID(:user)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		$errorCode = ERR::UNKNOWN;
	}
	// Table's just one row, one column
	$results = array("ID" => $stmt->fetchAll()[0]["UserID"], "Error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function registerUser($database, $username, $hash, $salt, $displayName) {
	$errorCode = ERR::OK;
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
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("Error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function verifyAndUpdateLoginToken($database, $userID, $oldToken) {
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL VerifyAndUpdateLoginToken(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $oldToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("Token" => $sel[0]['@newToken'], "Error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function getPublicUserDetails($database, $userID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetPublicUserDetails(:id)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$out = $stmt->fetchAll();
	if(count($out) != 0){
		$results = $out[0];
	} else $results[SP::ERROR] = ERR::USER_NOT_EXIST;
	$results[SP::ERROR] = $errorCode;
	//$results = array("DisplayName" => $out[0]['DisplayName'], "Location" => $out[0]['Location'], "Gender" => $out[0]['Gender'], "Error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function getPrivateUserDetails($database, $userID, $loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetPrivateUserDetails(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
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
		else $errorCode = ERR::UNKNOWN;
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
	$errorCode = ERR::OK;
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
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array("Token" => $sel[0]['@newToken'], "Error" => $errorCode);
	return $results;
}

function getForumInfo($database, $targetForumID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetForumInfo(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	// Only returns a single row
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results = $out[0];
	$reuslts[SP::ERROR] = $errorCode;
	return $results;
}

function getChildForums($database, $targetForumID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetChildForums(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
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
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetForumThreads(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	// This stored procedure returns multiple rows, so we can just add in the error code to the array returned by SQL.
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$out[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $out;
}

function getThreadPosts($database, $targetThreadID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetThreadPosts(:id)");
	$stmt->bindParam(":id", $targetThreadID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	// This stored procedure returns multiple rows, so we can just add in the error code to the array returned by SQL.
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$out[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $out;
}

function multigetPostDetails($database, $targetPostIDs){
	/* For efficiency, this function calls the stored procedure to fetch Thread details for an entire given list of postIDs. However, the limit is 50 posts per page.
	*/
	if(count($targetPostIDs) <= 50){
		$errorCode = ERR::OK;
		$stmt = $database->prepare("CALL GetPostDetails(:id)");
		$result = array();
		foreach($targetPostIDs as $postID){
			$stmt->bindParam(":id", $postID, PDO::PARAM_INT);
			try{
				$stmt->execute();
			}
			catch(PDOException $e){
				echo $e->getMessage();
				$errorCode = ERR::UNKNOWN;
			}
			$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(isset($out)){
				$result[$postID] = array(
					POST::USER_ID => $out[0][POST::USER_ID],
					POST::CONTENT => $out[0][POST::CONTENT],
					POST::MADE_AT => $out[0][POST::MADE_AT],
					POST::EDITED_AT => $out[0][POST::EDITED_AT],
					POST::EDITING_USER_ID => $out[0][POST::EDITING_USER_ID],
					SP::ERROR => $errorCode);
			}
			else $result[$postID] = array(SP::ERROR => ERR::POST_NOT_EXIST);
		}
		return $result;
	}
	else throw new RuntimeException("Too many posts requested at once!");
}