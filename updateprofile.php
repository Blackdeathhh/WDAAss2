<?php

$newLocation = $_POST['location'];
$newEmail = $_POST['email'];
$newGender = $_POST['gender'];
$newPostsPerPage = intval($_POST['postsPerPage'], 10);
$errors = array();

if(!validateLocation($newLocation)){
	$errors[] = "location=" . ERR::LOCATION_BAD;
}
if(!validateEmail($newEmail)){
	$errors[] = "email=" . ERR::EMAIL_BAD;
}
if(!validateGender($newGender)){
	$errors[] = "gender=" . ERR::SEX_BAD;
}
if(!validatePostsPerPage($newPostsPerPage)){
	$errors[] = "postsperpage=" . ERR::POSTS_PER_PAGE_BAD;
}

if(count($errors) == 0){
	$db = connectToDatabase();
	if($db){
		$results = modifyUserDetails($db, $_SESSION['id'], $_SESSION['token'], $newLocation, $newEmail, $newGender, $newPostsPerPage);
		//$_SESSION['token'] = $results[SP::TOKEN];
		switch($results[SP::ERROR]){
			case ERR::OK:
				break;
			case ERR::TOKEN_FAIL:
			case ERR::TOKEN_EXPIRED:
			case ERR::PERMIS_FAIL:
			case ERR::USER_NOT_EXIST:
			default:
				header("profile.php?error=". $results[SP::ERROR]);
				break;
		}
	}
}
else{
	header("profile.php?". implode("&", $errors));
}