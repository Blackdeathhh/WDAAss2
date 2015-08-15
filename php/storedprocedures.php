<?php
require_once("php/error.php");

class LOGIN{
	const ID = "UserID";
	const USERNAME = "Username";
	const HASH = "Passhash";
	const SALT = "Salt";
}

class ACTIVE{
	const TOKEN = "LoginToken";
	const USER_ID = "UserID";
	const EXPIRY = "ExpiresAt";
}

class PERMISSION{
	const USER_ID = "UserID";
	const LEVEL = "Level";
}

class USER{
	const ID = "UserID";
	const DISP_NAME = "DisplayName";
	const LOC = "Location";
	const EMAIL = "Email";
	const SEX = "Gender";
	const POSTS_PAGE = "PostsPerPage";
}

class FORUM{
	const ID = "ForumID";
	const PARENT_ID = "ParentForumID";
	const NAME = "ForumName";
	const SUBTITLE = "ForumSubtitle";
	const TOPIC = "Topic";
}

class THREAD{
	const ID = "ThreadID";
	const FORUM_ID = "InForumID";
	const STARTER_USER_ID = "StarterUserID";
	const TITLE = "ThreadTitle";
	const MADE_AT = "CreatedAt";
	const IS_STICKY = "isSticky";
	const VIEWS = "Views";
	const OPEN = "Open";
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
	$out = $stmt->fetchAll();
	$results;
	if(isset($out) && count($out) != 0){
		$results = array(LOGIN::SALT => $out[0][LOGIN::SALT], SP::ERROR => $errorCode);
	}
	else{
		$results = array(LOGIN::SALT => null, SP::ERROR => ERR::USERNAME_NOT_EXIST);
	}
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
	$results = array(SP::TOKEN => $sel[0]['@token'], SP::ERROR => $sel[0]['@error']);
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
	$out = $stmt->fetchAll();
	$results;
	if(isset($out) && count($out) != 0){
		$results = array(USER::ID => $out[0][USER::ID], SP::ERROR => $errorCode);
	}
	else{
		$results = array(USER::ID => null, SP::ERROR => SP::USERNAME_NOT_EXIST);
	}
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
	$results = array(SP::ERROR => $errorCode);
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
	$results = array(SP::TOKEN => $sel[0]['@newToken'], SP::ERROR => $errorCode);
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
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	} else $errorCode = ERR::USER_NOT_EXIST;
	$results[SP::ERROR] = $errorCode;
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
		if($e->getCode() == 2053) unset($out);
		else $errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll();
	$errorCode = $sel[0]['@error'];
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::USER_NOT_EXIST;
	}
	$results[SP::TOKEN] = $sel[0]['@newToken'];
	$results[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
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
	$results = array(SP::TOKEN => $sel[0]['@newToken'], SP::ERROR => $errorCode);
	$stmt->closeCursor();
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
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$reuslts[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
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
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$reuslts[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $results;
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
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$reuslts[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $results;
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
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::THREAD_NOT_EXIST;
	}
	$reuslts[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $results;
}

function getThreadInfo($database, $targetThreadID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetThreadInfo(:id)");
	$stmt->bindParam(":id", $targetThreadID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	// This stored procedure returns just one row
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::THREAD_NOT_EXIST;
	}
	$results[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $results;
}

function multigetPostDetails($database, $targetPostIDs){
	/* For efficiency, this function calls the stored procedure to fetch Thread details for an entire given list of postIDs. However, the limit is 50 posts per page. Might be lowered.
	Also, statement is intentionally re-prepared every iteration of the loop. Preparing it once and calling both closeCursor() and fetchAll() results in "General error: 2014 Cannot execute queries while other unbuffered queries are active". Enabling PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute does not fix this.
	*/
	if(count($targetPostIDs) <= 50){
		$errorCode = ERR::OK;
		$result = array();
		foreach($targetPostIDs as $postID){
			$stmt = $database->prepare("CALL GetPostDetails(:id)");
			$stmt->bindParam(":id", $postID, PDO::PARAM_INT);
			try{
				$stmt->execute();
			}
			catch(PDOException $e){
				echo $e->getMessage();
				$errorCode = ERR::UNKNOWN;
			}
			$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(isset($out) && count($out) != 0){
				$result[$postID] = array(
					POST::USER_ID => $out[0][POST::USER_ID],
					POST::CONTENT => $out[0][POST::CONTENT],
					POST::MADE_AT => $out[0][POST::MADE_AT],
					POST::EDITED_AT => $out[0][POST::EDITED_AT],
					POST::EDITING_USER_ID => $out[0][POST::EDITING_USER_ID],
					SP::ERROR => $errorCode);
			}
			else $result[$postID] = array(SP::ERROR => ERR::POST_NOT_EXIST);
			$stmt->closeCursor();
			$stmt = null;
		}
		return $result;
	}
	else throw new RuntimeException("Too many posts requested at once!");
}

function getForumAncestry($database, $targetForumID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetForumAncestry(:id)");
	$stmt->bindParam(":id", $targetForumID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results = array();
	if(isset($out)){
		foreach($out as $index => $col){
			$results[] = $col[FORUM::ID];
		}
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$results[SP::ERROR] = $errorCode;
}

function createPost($database, $userID, $targetThreadID, $content, $loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL CreatePost(:id, :thread, :content, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":thread", $targetThreadID, PDO::PARAM_INT);
	$stmt->bindParam(":content", $content, PDO::PARAM_STR);
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
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => $sel[0]['@newToken']);
	return $results;
}