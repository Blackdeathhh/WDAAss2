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
	require_once("php/posting.php");

	if(isset($_POST['forumid'])){
		$db = connectToDatabase();
		if($db){
			$info = getForumInfo($db, $_POST['forumid']);
			switch($info[SP::ERROR]){
				case ERR::OK:
					echo <<<EOT
<h2 class='title'>Creating a new thread in: <a href=forumview.php?forumid={$_POST['forumid']}>{$info[FORUM::TITLE]}</a></h2>
<form id="postform" method="POST" action="postsubmission.php">
	<input type="text" id="threadtitle" />
	<input type='hidden' id='forumid' name='forumid' value='{$_POST['forumid']}'>
EOT;
					echo createContentArea();
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
	else{
		echo "No forum specified. <a href='forumview.php'>Back to forums.</a>";
	}
?>
		<noscript>Javascript is required to make a thread.</noscript>
	</div>
</body>
</html>