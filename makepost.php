<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/newpost.css" />
	<script type="text/javascript" src="js/newPost.js"></script>
	<meta charset="UTF-8">
	<title>Posting</title>
</head>
<?php require("php/topbar.php"); ?>
<body>
	<div class="maindiv">

<?php
	session_start();
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");
	require_once("php/posting.php");

	if(isset($_POST['threadid'])){
		$db = connectToDatabase();
		if($db){
			$info = getThreadInfo($db, $_SESSION['id'], $_POST['threadid']);
			switch($info[SP::ERROR]){
				case ERR::OK:
					echo <<<EOT
<h2 class='title'>Posting to: <a href=threadview.php?threadid={$_POST['threadid']}>{$info[THREAD::TITLE]}</a></h2>
<form id="postform" method="POST" action="postsubmission.php">
	<input type='hidden' id='threadid' name='threadid' value='{$_POST['threadid']}'>
EOT;
					if(isset($_POST['editstr'])){
						echo createContentArea($_POST['editstr']);
					}
					else{
						echo createContentArea();
					}
/*	<input type="hidden" id="content" name="content" />
	<div id='postcontent' contenteditable>
EOT;
					// editstr can be set if a post failed, and postsubmission sends us back here; no post has been made, but we can edit what we did have
					if(isset($_POST['editstr'])){
						echo $_POST['editstr'];
					}
					echo <<<EOT
</div>
	<input type='button' id='post' value='Post' onclick='submitPost()' />
EOT;*/
					echo "</form>";
					break;
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
	<input type="hidden" id="content" name="content" />
EOT;
		if(isset($_POST['editstr'])){
			echo "<div id='postcontent' contenteditable>" . $_POST['editstr'] . "</div>";
		}
		else{
			$db = connectToDatabase();
			$details = multigetPostDetails($db, $_SESSION['id'], array($_POST['editid']))[$_POST['editid']];
			switch($outPost[SP::ERROR]){
				case ERR::OK:
					echo "<div id='postcontent' contenteditable>" . $details[POST::CONTENT] . "</div>";
					break;
				default:
					echo $ERRORS[$details[SP::ERROR]];
					break;
			}
		}
		echo <<<EOT
	<input type='button' id='post' value='Post' onclick='submitPost()' /></form>
EOT;
	}
	else{
		echo "No thread specified. <a href='forumview.php'>Back to forums.</a>";
	}
?>
		<noscript>Javascript is required to make a post.</noscript>
	</div>
</body>
</html>