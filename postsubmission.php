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
		/* Search the string to find any HTML tags that we have forbidden; that is, anything other than b, u, i, a, img, or br. If we do find a tag, we can clean it up and set a link back to edit the post. */
		$db = connectToDatabase();
		if($db){
			$postToThread = (isset($_POST['threadid'])) ? $_POST['threadid'] : null;
			if(isset($_POST['newthreadtitle']) && $postToThread == null){
				// We're making a new thread as well, then posting to it
				// We have to only make a thread if we have content to post.
				$result = createThread($db, $_SESSION['id'], $_POST['forumid'], $_POST['newthreadtitle'], $_SESSION['token']);
				//$_SESSION['token'] = $result[SP::TOKEN];
				switch($result[SP::ERROR]){
					case ERR::OK:
						$postToThread = $result[THREAD::ID];
						break;
				}
			}
			if($postToThread != null){
				// We're making a new post
				$result = createPost($db, $_SESSION['id'], $postToThread, $_POST['content'], $_SESSION['token']);
				//$_SESSION['token'] = $result[SP::TOKEN];
				switch($result[SP::ERROR]){
					// If the page number's too high, you'll just go to the final page.
					case ERR::OK:
						echo "Post made successfully! <a href='threadview.php?threadid=". $postToThread ."&page=99999'>Back to thread</a>.";
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
			elseif(isset($_POST['editid'])){
				$result = editPost($db, $_SESSION['id'], $_POST['editid'], $_POST['content'], $_SESSION['token']);
				switch($result[SP::ERROR]){
					case ERR::OK:
						$info = multigetPostDetails($db, array($_POST['editid']))[$_POST['editid']];
						echo "Post made successfully! <a href='threadview.php?threadid=". $info[POST::THREAD_ID] ."'>Back to thread</a>.";
						break;
					case ERR::THREAD_LOCKED:
						echo "The specified thread is locked.";
						break;
					case ERR::PERMIS_FAIL:
						echo "You do not have sufficient permissions to do this.";
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
				// We're editing an existing post
			}
			else{
				// User is a big clown and hasn't provided enough information to actually do anything >:T
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