<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
</head>
<body>
<div id="topbar">
	<img src="img/header.png" />
</div>

<div class="maindiv">
<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

if(isset($_POST['modDetails'])){
	echo "[DEBUG]POST Set; modifying details...";
	
	$newLocation = $_POST['location'];
	$newEmail = $_POST['email'];
	$newGender = $_POST['gender'];
	$newPostsPerPage = $_POST['postsPerPage'];
	
	/*Verify all the data; like Gender has to be 1 char, etc.
	
	*/
	
	$db = connectToDatabase();
	$results = modifyUserDetails($db, $_SESSION['id'], $_SESSION['token'], $newLocation, $newEmail, $newGender, $newPostsPerPage);
	$_SESSION['token'] = $results['token'];
	if($results['error'] == ERR::OK){
		echo "User details updated successfully!";
	}
	else{
		echo "Failed to update user details.";
	}
}

$userID;
$isOwnProfile;

if(isset($_GET['profileID']))
{
	$userID = $_GET['profileID'];
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
	if($isOwnProfile){
		$results = getPrivateUserDetails($db, $userID, $_SESSION['token']);
		$_SESSION['token'] = $results['token'];
		$errorCode = $results['error'];
		$displayName = $results['displayName'];
		$location = $results['location'];
		$gender = $results['gender'];
		$email = $results['email'];
		$postsPerPage = $results['postsPerPage'];
	}
	else{
		$results = getPublicUserDetails($db, $userID);
		$errorCode = $results['error'];
		$displayName = $results['displayName'];
		$location = $results['location'];
		$gender = $results['gender'];
	}
}
else{
	echo "Please log in or select a profile to view.";
}
if($errorCode == ERR::OK){
	/*We have to display the avatar, buttons to change it. Also some sort of notification if you have any new private messages, along with a link to go and view them.
	Fields that display user details. If it's our profile, we show more and they can be modified
	Private Messages belong on a separate page. Friends probably do, as well. They could go together on a separate page 'friends.php', which could show a list of all friends, links to their profiles, ability to send them PMs.
	Private Messages viewing should */
	// If you want to customize that upload thing, wrap it in a label, make input's display: none. Then, place a <span> after it, inside the label, and style that how you like.
	echo <<<EOT
<h2>{$displayName}'s Profile</h2><div>
	<div class="profileavatar">
		<img class="avatar" src="avatar/{$userID}.jpg" />
EOT;

	if($isOwnProfile){
		echo <<<EOT
<form>
	<input type="file" name="img" required />
	<input type-"submit" name="submit" value="Upload Image" />
</form>
EOT;
	}
	echo "</div><div><ol>";
	if($isOwnProfile){
		echo <<<EOT
<form action="profile.php" method="POST">
	<li><label>Location: </label><input type="text" name="location" id="location" value="{$location}" /></li>
	<li>
		<label>Gender: </label>
		<select>
EOT;
		echo "<option value='M'" . (($gender == "M") ?  : "") .">Male</option>";
		echo "<option value='F'" . (($gender == "F") ? " selected " : "") .">Female</option>";
		echo "<option value='O'" . (($gender == "O") ? " selected " : "") .">Other</option>";
		echo "<option value='N'" . (($gender == "N") ? " selected " : "") .">Not Provided</option>";
		echo <<<EOT
		</select>
	</li>
	<li><label>Email: </label><input type="email" name="email" id="email" value="{$email}" /></li>
	<li>
		<label>Posts Per Page: </label>
		<select>
EOT;
		for($i = 10; $i <= 50; $i += 5){
			echo "<option value='$i'";
			if($i == $postsPerPage) echo " selected ";
			echo ">$i</option>";
		}
		echo <<<EOT
		</select>
	</li>
	<li><input type="submit" id="submit" name="modDetails" value="Modify" /></li>
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
	echo "</ol></div>";
	echo "</div>";
}
else{
	echo "<h2>Error</h2>";
}
?>
</div>
</body>
</html>