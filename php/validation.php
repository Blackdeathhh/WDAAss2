<?php
/* These are the characters that are in the ASCII range decimal 32 to 126 : 
!"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~
*/
function validateUsername($username){
	// Max 20 chars, no spaces
	$regex = "/^[!-~]{1,20}$/";
	return preg_match($regex, $username);
}

function validatePassword($password){
	// Max 72 chars (Because that's what BCRYPT will truncate to
	$regex = "/^.{1,72}$/";
	return preg_match($regex, $password);
}

function validateDisplayname($displayname){
	// Max 20 chars, no spaces
	$regex = "/^[!-~]{1,20}$/";
	return preg_match($regex, $displayname);
}

function validateGender($gender){
	$regex = "/^[MFNO]$/";
	return preg_match($regex, $gender);
}

function validatePostsPerPage($postsPerPage){
	// Any number between 10 and 30; we really don't care if it's a multiple of 5 or not
	return ($postsPerPage >= 10 && $postsPerPage <= 30);
}

function validateLocation($location){
	// Max 30 chars, no spaces, or empty string
	if($location == "") return true;
	$regex = "/^[!-~]{1,30}$/";
	return preg_match($regex, $location);
}

function validateEmail($email){
	/* Max 80 chars. Requires...
	a sequence of non-@ characters
	then a @
	then a sequence of non-@ and non-. characters
	then a .
	then a sequence of 2-5 non-@ and non-. characters
	*/
	if($email == "") return true;
	if(strlen($email) > 0 && strlen($email) <= 80)
	{
		$regex = "/^[^@]+@[^@\.]+\.[^@\.]{2,5}$/";
		return preg_match($regex, $email);
	}
	return false;
}

function validateTimeZone($timeZone){
	$regex = "/^[\+\-]\d{2}:\d{2}$/";
	return preg_match($regex, $timeZone);
}

function validateForumName($name){ // 50 char
	return (strlen($name) > 0 && strlen($name) < 50);
}

function validateForumSubtitle($subtitle){ // 100 varchar
	if($subtitle == "") return true;
	return (strlen($subtitle) > 0 && strlen($subtitle) < 100);
}

function validateForumTopic($topic){ // 50 char
	return (strlen($topic) > 0 && strlen($topic) < 50);
}

function validateMessageTitle($msgTitle){ // 30 char
	return (strlen($$msgTitle) > 0 && strlen($$msgTitle) < 30);
}
