<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/newpost.css" />
	<meta charset="UTF-8">
</head>
<?php require("php/topbar.php"); ?>
<body>
	<div class="maindiv">
<?php
	session_start();
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");
	
	if(isset($_POST['content'])){
		//if(isset$_POST['editID'])) - 
		/* Search the string to find any HTML tags that we have forbidden; that is, anything other than b, u, i, a, img, or br. If we do find a tag, we can clean it up and set a link back to edit the post. */
		$db = connectToDatabase();
		if($db){
			$result = createPost($database, $_SESSION['id'], $_POST['threadid'], $_POST['content'], $_SESSION['token']);
			switch($result[SP::ERROR){
				// If the page number's too high, you'll just go to the final page.
				case ERR::OK:
					echo "Post made successfully! <a href='threadview.php?threadid=". $_POST['threadid'] ."&page=99999'>Back to thread</a>.";
					break;
				case ERR::THREAD_NOT_EXIST:
					echo "The specified thread does not, or no longer, exists.";
					break;
				case ERR::THREAD_LOCKED:
					echo "The specified thread is locked.";
					break;
				case ERR::PERMIS_FAIL:
					echo "You must <a href='login.php'>log in</a> to do this.";
					break;
				case ERR::TOKEN_FAIL:
				case ERR::TOKEN_EXPIRED:
					echo "Your session has expired; please <a href='login.php'>log in</a> again.";
					break;
				case ERR::UNKNOWN:
				default:
					echo "An unknown error occurred. Please try again later.";
					break;
			}
		}
		else{
			echo "Could not connect to server. Please try again later.";
			// Echo link back with POST data
		}
	}
	else{
		echo "No content specified. <a href='makepost.php'>Back to post.</a>";
	}
?>
	</div>
</body>
</html>