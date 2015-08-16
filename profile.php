<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<meta charset="UTF-8">
</head>
<body>
<?php require("php/topbar.php"); ?>

<div class="maindiv">
<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");
require_once("php/constants.php");

/*$errorMessage = "";

if(isset($_POST['modDetails'])){
	$newLocation = $_POST['location'];
	$newEmail = $_POST['email'];
	$newGender = $_POST['gender'];
	$newPostsPerPage = intval($_POST['postsPerPage'], 10);

	$locationValid = validateLocation($newLocation);
	$emailValid = validateEmail($newEmail);
	$genderValid = validateGender($newGender);
	$postsPerPageValid = validatePostsPerPage($newPostsPerPage);

	$db = connectToDatabase();
	if($db){
		$results = modifyUserDetails($db, $_SESSION['id'], $_SESSION['token'], $newLocation, $newEmail, $newGender, $newPostsPerPage);
		//$_SESSION['token'] = $results[SP::TOKEN];
		// If any of these errors occur, then the same thing will happen during information retrieval on, so we can just check/state the error has occurred there
		switch($results[SP::ERROR]){
			case ERR::OK:
				$errorMessage = "User details updated successfully!";
				break;
			case ERR::TOKEN_FAIL:
			case ERR::TOKEN_EXPIRED:
			case ERR::PERMIS_FAIL:
			case ERR::USER_NOT_EXIST:
			default:
				$errorMessage = "User details not updated.";
				break;
		}
	}
}*/

$userID;
$isOwnProfile;

if(isset($_GET['profileid']))
{
	$userID = $_GET['profileid'];
	$isOwnProfile = false;
}
elseif(isset($_SESSION['id'])){
	$userID = $_SESSION['id'];
	$isOwnProfile = true;
}
else{
	$userID = 0;
	$isOwnProfile = false;
}

$errorCode;
$displayName;
$location;
$gender;
$email;
$postsPerPage;

if($userID != 0){
	$db = connectToDatabase();
	if($db){
		if($isOwnProfile){
			$results = getPrivateUserDetails($db, $userID, $_SESSION['token']);
			//$_SESSION['token'] = $results[SP::TOKEN];
			$errorCode = $results[SP::ERROR];
			$displayName = $results[USER::DISP_NAME];
			$location = $results[USER::LOC];
			$gender = $results[USER::SEX];
			$email = $results[USER::EMAIL];
			$postsPerPage = $results[USER::POSTS_PAGE];
		}
		else{
			$results = getPublicUserDetails($db, $userID);
			$errorCode = $results[SP::ERROR];
			$displayName = $results[USER::DISP_NAME];
			$location = $results[USER::LOC];
			$gender = $results[USER::SEX];
		}
		switch($errorCode){
			case ERR::OK:
			/*We have to display the avatar, buttons to change it. Also some sort of notification if you have any new private messages, along with a link to go and view them.
			Fields that display user details. If it's our profile, we show more and they can be modified
			Private Messages belong on a separate page. Friends probably do, as well. They could go together on a separate page 'friends.php', which could show a list of all friends, links to their profiles, ability to send them PMs.
			Private Messages viewing should */
			// If you want to customize that upload thing, wrap it in a label, make input's display: none. Then, place a <span> after it, inside the label, and style that how you like.
				echo <<<EOT
	<h2 class='title'>{$displayName}'s Profile</h2><div>
		<div class="profileavatar">
			<img class="avatar" src="avatar/{$userID}.jpg" />
EOT;
				if($isOwnProfile){
					echo <<<EOT
			<form method="POST" action="uploadavatar.php" enctype="multipart/form-data">
				<input type="file" name="newavatar" required />
				<input type="submit" name="submit" value="Upload Image" />
			</form>
EOT;
				}
				echo "</div><div><ol>";
				if($isOwnProfile){
					echo <<<EOT
			<form action="updateprofile.php" method="POST">
				<li><label>Location: </label><input type="text" name="location" id="location" value="{$location}" /></li>
				<li>
					<label>Gender: </label>
					<select name="gender">
EOT;
					echo "<option value='M'" . (($gender == "M") ?  : "") .">Male</option>";
					echo "<option value='F'" . (($gender == "F") ? " selected " : "") .">Female</option>";
					echo "<option value='O'" . (($gender == "O") ? " selected " : "") .">Other</option>";
					echo "<option value='N'" . (($gender == "N") ? " selected " : "") .">Not Provided</option>";
					echo <<<EOT
					</select>
				</li>
				<li><label>Email: </label><input type="text" name="email" id="email" value="{$email}" /></li>
				<li>
					<label>Posts Per Page: </label>
					<select name="postsperpage">
EOT;
					for($i = 10; $i <= 30; $i += 5){
						echo "<option value='$i'";
						if($i == $postsPerPage) echo " selected ";
						echo ">$i</option>";
					}
					echo <<<EOT
					</select>
				</li>
				<li><input type="submit" id="submit" value="Modify" /></li>
			</form>
EOT;
				}
				else{
					echo "<li><label>Location: </label>{$location}</li><li><label>Gender: </label>";
					switch($gender){
						case "M":
							echo "Male";
						break;
						case "F":
							echo "Female";
						break;
						case "O":
							echo "Other";
						break;
						case "N":
							echo "Not Provided";
						break;
					}
					echo "</li>";
				}
				echo "</ol></div></div>";
		}
	}
	else{
		echo "<h2 class='title'>Error</h2>";
	}
}
else{
	echo "<p>Please <a href='login.php'>log in</a> or select a profile to view.</p>";
}

if(isset($_GET['location'])){
	switch($_GET['location']){
		case ERR::LOCATION_BAD;
			echo "<p>Your location may only be a maximum of 30 characters.</p>";
			break;
	}
}
if(isset($_GET['email'])){
	switch($_GET['email']){
		case ERR::EMAIL_BAD;
			echo "<p>Your Email must be valid with a maximum of 80 characters.</p>";
			break;
	}
}
if(isset($_GET['gender'])){
	switch($_GET['gender']){
		case ERR::SEX_BAD;
			echo "<p>Your Gender must be one of: Male, Female, Other, Not Provided.</p>";
			break;
	}
}
if(isset($_GET['postsperpage'])){
	switch($_GET['postsperpage']){
		case ERR::POSTS_PER_PAGE_BAD;
			echo "<p>Your Posts per page must be between 10 and 30.</p>";
			break;
	}
}
if(isset($_GET['error'])){
	switch($_GET['error']){
		case ERR::OK:
			echo "<p>Your profile has been updated successfully!</p>";
			break;
		case ERR::TOKEN_EXPIRED:
			echo "<p>Your session has expired, please log in again.</p>";
			break;
		case ERR::PERMIS_FAIL:
			echo "<p>You do not have the permissions required to do that.</p>";
			break;
		case ERR::TOKEN_FAIL:
		case ERR::USER_NOT_EXIST:
			echo "<p>This problem shouldn't be happening...please contact me about it and quote the error code". $_GET['error'] ."</p>";
			break;
		case ERR::UNKNOWN:
		default:
			echo "<p>An unknown error occured, please try again later.</p>";
			break;
	}
}
?>
</div>
</body>
</html>