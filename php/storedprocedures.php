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
	const REQUIRED_LEVEL = "PermissionLevelRequired";
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
// These consts do not correspond to table columns
	const LATEST_POST_AT = "LatestPost";
}

class POST{
	const ID = "PostID";
	const THREAD_ID = "InThreadID";
	const USER_ID = "PostingUserID";
	const CONTENT = "Content";
	const MADE_AT = "CreatedAt";
	const EDITED_AT = "LastEdited";
	const EDITING_USER_ID = "EditedByUserID";
// These consts do not correspond to table columns
	const LATEST_POST_ID = "LatestPostID";
}

class FRIEND{
	const ID = "UserID";
	const FRIEND_ID = "HasFriend";
}

class SP{
	const ERROR = "Error";
	const TOKEN = "Token";
}

class AGGR{
	const NUM_POSTS = "Posts";
	const NUM_THREADS = "Threads";
}

$P_LEVELS = array(
0 => "Demoted User",
1 => "Standard User",
2 => "Privileged User",
3 => "Super User",
4 => "Moderator",
5 => "Super Moderator",
10 => "Administrator"
);

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
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
	$stmt = $database->prepare("CALL Login(:user, :hash, @level, @id, @token, @error)");
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
	$sel = $database->query("SELECT @level, @id, @token, @error")->fetchAll(PDO::FETCH_ASSOC);
	$results = array(USER::ID => intval($sel[0]['@id'], 10), SP::TOKEN => intval($sel[0]['@token'], 10), SP::ERROR => intval($sel[0]['@error'], 10), PERMISSION::LEVEL => intval($sel[0]['@level'], 10));
	//$results = array("token" => $loginToken, "error" => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function logout($database, $userID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL Logout(:id, :token, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	if($errorCode == ERR::OK) unset($loginToken);
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
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results;
	if(isset($out) && count($out) != 0){
		$results = array(USER::ID => intval($out[0][USER::ID], 10), SP::ERROR => $errorCode);
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
	$errorCode = intval($sel[0]['@error'], 10);
	$results = array(SP::ERROR => $errorCode);
	$stmt->closeCursor();
	return $results;
}

function verifyUser($database, $requiredPermission, $userID, &$token) {
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL VerifyUser(:id, :permission, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":permission", $requiredPermission, PDO::PARAM_INT);
	$stmt->bindParam(":token", $token, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$results = array(SP::TOKEN => intval($sel[0]['@newToken'], 10), SP::ERROR => $errorCode);
	$token = intval($sel[0]['@newToken'], 10);
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
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	} else $errorCode = ERR::USER_NOT_EXIST;
	$results[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $results;
}

function getPrivateUserDetails($database, $userID, &$loginToken){
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
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out[0];
	}
	else{
		$errorCode = ERR::USER_NOT_EXIST;
	}
	$results[SP::TOKEN] = intval($sel[0]['@newToken'], 10);
	$loginToken = intval($sel[0]['@newToken'], 10);
	$results[SP::ERROR] = $errorCode;
	return $results;
}

function modifyUserDetails($database, $userID, &$loginToken, $newLocation, $newEmail, $newGender, $newPostsPerPage)
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
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$results = array(SP::TOKEN => intval($sel[0]['@newToken'], 10), SP::ERROR => $errorCode);
	$loginToken = intval($sel[0]['@newToken'], 10);
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
		// Returns a literal bit 1 which ends up as a string. Therefore, get ASCII code
		//$results[FORUM::ALLOW_THREAD] = ord($results[FORUM::ALLOW_THREAD]) == 1;
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$results[SP::ERROR] = $errorCode;
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
		$results = $out;
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$results[SP::ERROR] = $errorCode;
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
		for($i = 0; $i != count($out); ++$i){
			$results[$i] = $out[$i];
			$results[$i][THREAD::OPEN] = ord($results[$i][THREAD::OPEN]) == 1;
		}
	}
	else{
		$errorCode = ERR::FORUM_NOT_EXIST;
	}
	$results[SP::ERROR] = $errorCode;
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
		$results = $out;
	}
	else{
		$errorCode = ERR::THREAD_NOT_EXIST;
	}
	$results[SP::ERROR] = $errorCode;
	$stmt->closeCursor();
	return $results;
}

function viewThread($database, $targetThreadID){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL ViewThread(:id)");
	$stmt->bindParam(":id", $targetThreadID, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	// This stored procedure returns nothing.
	$results = array();
	$results[SP::ERROR] = $errorCode;
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
		$results[THREAD::OPEN] = ord($results[THREAD::OPEN]) == 1;
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
		$results = array();
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
				$results[$postID] = array(
					POST::USER_ID => $out[0][POST::USER_ID],
					POST::THREAD_ID => $out[0][POST::THREAD_ID],
					POST::CONTENT => $out[0][POST::CONTENT],
					POST::MADE_AT => $out[0][POST::MADE_AT],
					POST::EDITED_AT => $out[0][POST::EDITED_AT],
					POST::EDITING_USER_ID => $out[0][POST::EDITING_USER_ID],
					SP::ERROR => $errorCode);
			}
			else $results[$postID] = array(SP::ERROR => ERR::POST_NOT_EXIST);
			$stmt->closeCursor();
			$stmt = null;
		}
		return $results;
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

function createPost($database, $userID, $targetThreadID, $content, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL CreatePost(:id, :thread, :content, @postID, :token, @newToken, @error)");
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
	$sel = $database->query("SELECT @error, @newToken, @postID")->fetchAll();
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10), POST::ID => intval($sel[0]['@postID']));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function createThread($database, $userID, $targetForumID, $title, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL Createthread(:id, :forum, :title, @threadID, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":forum", $targetForumID, PDO::PARAM_INT);
	$stmt->bindParam(":title", $title, PDO::PARAM_STR);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @threadID, @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10), THREAD::ID => intval($sel[0]['@threadID']));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function deletePost($database, $userID, $targetPostID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL DeletePost(:id, :post, :token, @newtoken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":post", $targetPostID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function editPost($database, $userID, $targetPostID, $newcontent, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL EditPost(:id, :post, :content, :token, @newtoken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":post", $targetPostID, PDO::PARAM_INT);
	$stmt->bindParam(":content", $newcontent, PDO::PARAM_STR);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function createForum($database, $forumName, $forumSubtitle, $forumTopic, $forumParent, $userID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL CreateForum(:name, :subtitle, :topic, :parent, :userID, :token, @newtoken, @error)");
	$stmt->bindParam(":name", $forumName, PDO::PARAM_STR);
	$stmt->bindParam(":subtitle", $forumSubtitle, PDO::PARAM_STR);
	$stmt->bindParam(":topic", $forumTopic, PDO::PARAM_STR);
	$stmt->bindParam(":parent", $forumParent, PDO::PARAM_INT);
	$stmt->bindParam(":userID", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function addFriend($database, $userID, $friendID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL AddFriend(:id, :friendid, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":friendid", $friendID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function removeFriend($database, $userID, $friendID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL RemoveFriend(:id, :friendid, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":friendid", $friendID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function getFriends($database, $userID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetFriends(:id, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$stmt = null;

	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out;
	}
	else{
		$errorCode = ERR::USER_NOT_EXIST;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$loginToken = intval($sel[0]['@newToken'], 10);
	$results[SP::ERROR] = $errorCode;
	return $results;
}

function createMessage($database, $userID, $recipientID, $title, $content, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL CreateMessage(:id, :recipient, :title, :content, :token, @newToken, @error)");
	$stmt->bindParam(":id", $userID, PDO::PARAM_INT);
	$stmt->bindParam(":recipient", $recipientID, PDO::PARAM_INT);
	$stmt->bindParam(":title", $title, PDO::PARAM_STR);
	$stmt->bindParam(":content", $content, PDO::PARAM_STR);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$stmt->closeCursor();
	$results = array(SP::ERROR => $errorCode, SP::TOKEN => intval($sel[0]['@newToken'], 10));
	$loginToken = intval($sel[0]['@newToken'], 10);
	return $results;
}

function getMessages($database, $userID, $senderUserID, $receiverUserID, &$loginToken){
	$errorCode = ERR::OK;
	$stmt = $database->prepare("CALL GetMessages(:id, :sender, :receiver, :token, @newToken, @error)");
	$stmt->bindParam(":id", $senderUserID, PDO::PARAM_INT);
	$stmt->bindParam(":sender", $senderUserID, PDO::PARAM_INT);
	$stmt->bindParam(":receiver", $receiverUserID, PDO::PARAM_INT);
	$stmt->bindParam(":token", $loginToken, PDO::PARAM_INT);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage();
		$errorCode = ERR::UNKNOWN;
	}
	$out = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor();
	$stmt = null;

	$results = array();
	if(isset($out) && count($out) != 0){
		$results = $out;
	}
	else{
		$errorCode = ERR::USER_NOT_EXIST;
	}
	$sel = $database->query("SELECT @error, @newToken")->fetchAll(PDO::FETCH_ASSOC);
	$errorCode = intval($sel[0]['@error'], 10);
	$loginToken = intval($sel[0]['@newToken'], 10);
	$results[SP::ERROR] = $errorCode;
	return $results;
}