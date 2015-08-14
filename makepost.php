<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/newpost.css" />
	<script type="text/javascript" src="js/newPost.js"></script>
	<meta charset="UTF-8">
</head>
<?php require("php/topbar.php"); ?>
<body>
	<div class="maindiv">

<?php
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");

	if(isset($_POST['threadid'])){
		$db = connectToDatabase();
		if($db){
			$info = getThreadInfo();
			switch($info[SP::ERROR]){
				case ERR::OK:
					echo <<<EOT
<h2 class='title'>Posting to: {$info[THREAD::TITLE]}</h2>
<form id="postform" method="POST" action="postsubmission.php">
	<input type='hidden' id='threadid' name='threadid' value='{$_POST['threadid']}'>
	<input type="hidden" id="content" name="content" />
	<div id='postcontent' contenteditable>
EOT;
					// editstr can be set if a post failed, and postsubmission sends us back here; no post has been made, but we can edit what we did have
					if(isset($_POST['editstr'])){
						echo $_POST['editstr'];
					}
					echo <<<EOT
</div>
	<input type='button' name='post' id='post' value='Post' onclick='submitPost()' />
</form>
EOT;
				case ERR::THREAD_NOT_EXIST;
					echo "The thread does not or no longer exists. Back to <a href='forumview.php'>forums</a>.";
					break;
				case ERR::UNKNOWN:
				default:
					echo "An unknown error occured. Please try again later.";
					break;
			}
		}
	}
	elseif(isset($_POST['editid'])){
		// If we're editing a particular post, we don't need a threadid; the postid is enough.
		echo <<<EOT
<h2 class='title'>Editing post</h2>
<form id="postform" method="POST" action="postsubmission.php">
	<input type='hidden' id='editid' name='editid' value='{$_POST['editid']}'/>
EOT;
		if(isset($_POST['editstr'])){
			echo $_POST['editstr'];
		}
		else{
			$db = connectToDatabase();
			$details = multigetPostDetails($db, array($_POST['editid']));
			switch($details[SP::ERR]){
				case ERR::OK:
					echo "<div id='postcontent' contenteditable>". $details[0][POST::CONTENT] . "</div>";
					break;
				case ERR::POST_NOT_EXIST:
					echo "That post does not or no longer exists.";
					break;
			}
		}
		echo "</form>";
	}
	else{
		echo "No thread specified. <a href='forumview.php'>Back to forums.</a>";
	}
?>
		<noscript>Javascript is required to make a post.</noscript>
	</div>
</body>
</html>