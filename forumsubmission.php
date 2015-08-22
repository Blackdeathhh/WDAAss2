<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/newpost.css" />
	<script type="text/javascript" src="js/newPost.js"></script>
	<meta charset="UTF-8">
	<title>New Forum</title>
</head>
<?php require("php/topbar.php"); ?>
<body>
	<div class="maindiv">
<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");
require_once("php/validation.php");

$forumParent = null;
if(isset($_POST['parentid']) && $_POST['parentid'] != "") $forumParent = intval($_POST['parentid'], 10);
$forumName = $_POST['name'];
$forumSubtitle = $_POST['subtitle'];
$forumTopic = $_POST['topic'];

$forumNameValid = validateForumName($forumName);
$forumSubtitleValid = validateForumSubtitle($forumSubtitle);
$forumTopicValid = validateForumTopic($forumTopic);

if($forumNameValid && $forumSubtitleValid && $forumTopicValid){
	$db = connectToDatabase();
	if($db){
		$results = createForum($db, $forumName, $forumSubtitle, $forumTopic, $forumParent, $_SESSION['id'], $_SESSION['token']);
		switch($results[SP::ERROR]){
			case ERR::OK:
				echo "<p>Forum successfully created! <a href='forumview.php";
				if($forumParent != null) echo "?forumid=". $forumParent;
				echo "'>Back to forums.</a></p>";
				break;
			case ERR::TOKEN_EXPIRED:
			case ERR::TOKEN_FAIL:
			case ERR::USER_NO_TOKEN:
				header("Location: logout.php?error=". $results[SP::ERROR]);
				break;
			default:
				echo "<p>Error creating forum: ". $ERRORS[$results[SP::ERROR]] ."</p>";
				break;
		}
	}
	else{
		echo "<p>Error connecting to database.</p>";
	}
}
?>
</div>
</body>
</html>