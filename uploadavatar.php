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

if(is_array($_FILES['newavatar'])){
	$file = $_FILES['newavatar'];
	if($file['error'] == UPLOAD_ERR_OK){
		if($file['size'] <= AVATAR_MAX_SIZE){
			$dimensions = getimagesize($file['tmp_name']);
			if($dimensions){
				if($dimensions[0] == AVATAR_WIDTH && $dimensions[1] == AVATAR_HEIGHT){
					$db = connectToDatabase();
					$results = verifyAndUpdateLoginToken($db, $_SESSION['id'], $_SESSION['token']);
					switch($results[SP::ERROR]){
						case ERR::OK:
							//$_SESSION['token'] = $results[SP::TOKEN];
							$success = move_uploaded_file($file['tmp_name'], "avatar/" . $_SESSION['id'] . ".jpg");
							if($success){
								echo "<p>File successfully uploaded!</p>";
							}
							else{
								echo "<p>File not uploaded successfully. Please try again later, or contact the web master.</p>";
							}
							break;
						case ERR::TOKEN_EXPIRED:
							echo "<p>Your session has expired; please <a href='login.php'>log in</a> again</p>";
							break;
						case ERR::TOKEN_FAIL:
						case ERR::ACC_IN_USE:
						case ERR::USER_NO_TOKEN:
							echo "<p>Hey, what do you think you're doing? Go away, you're not this user you big fat liar.</p>";
					}
				}
				else{
					echo "<p>The image must be 100 x 100 pixels.</p>";
				}
			}
			else{
				echo "<p>The file is not a valid image.</p>";
			}
		}
		else{
			echo "<p>The image must be no larger than 1 Megabyte.</p>";
		}
	}
	else{
		switch($file['error'])
		{
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				echo "<p>The image must be no larger than 1 Megabyte.</p>";
			break;
			case UPLOAD_ERR_PARTIAL:
				echo "<p>File upload failed; please try again later.</p>";
			break;
			case UPLOAD_ERR_NO_FILE:
				echo "<p>No file was uploaded.</p>";
			break;
		}
	}
}
echo "<p><a href='profile.php'>Return to your profile</a></p>";
?>
</div>
</body>
</html>