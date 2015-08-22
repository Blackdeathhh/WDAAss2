<?php
session_start();
require_once("php/database.php");
require_once("php/validation.php");
require_once("php/error.php");
require_once("php/storedprocedures.php");

$newLocation = $_POST['location'];
$newEmail = $_POST['email'];
$newGender = $_POST['gender'];
$newPostsPerPage = intval($_POST['postsperpage'], 10);
$newTimeZone = $_POST['timezone'];
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
if(!validateTimeZone($newTimeZone)){
	$errors[] = "timezone=" . ERR::TIME_ZONE_BAD;
}

if(count($errors) == 0){
	$db = connectToDatabase();
	if($db){
		$results = modifyUserDetails($db, $_SESSION['id'], $_SESSION['token'], $newLocation, $newEmail, $newGender, $newPostsPerPage, $newTimeZone);
//		header("Location: profile.php?error=". $results[SP::ERROR]);
		switch($results[SP::ERROR]){
			case ERR::TOKEN_EXPIRED:
			case ERR::TOKEN_FAIL:
			case ERR::USER_NO_TOKEN:
				header("Location: logout.php?error=". $results[SP::ERROR]);
				break;
			case ERR::OK:
			default:
				header("Location: profile.php?error=". $results[SP::ERROR]);
				break;
		}
	}
}
else{
	header("Location: profile.php?". implode("&", $errors));
}