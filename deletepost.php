<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/newpost.css" />
	<script type="text/javascript" src="js/newPost.js"></script>
	<meta charset="UTF-8">
	<title>Deleting post</title>
</head>
<?php require("php/topbar.php"); ?>
<body>
	<div class="maindiv">

<?php
	session_start();
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");

	if(isset($_GET['confirm']) && $_GET['confirm'] == 'Delete'){
		$db = connectToDatabase();
		if($db){
			$results = deletePost($db, $_GET['postid'], $_SESSION['id'], $_SESSION['token']);
			switch($results[SP::ERROR]){
				case ERR::OK:
					echo "<p>Post deleted successfully.</p>";
					break;
				case ERR::PERMIS_FAIL:
					echo "<p>You are not permitted to do this.</p>";
					break;
				default:
					echo "<p>Error: ". $ERRORS[$results[SP::ERROR]] ."</p>";
					break;
			}
		}
		else{
			echo "<p>Could not connect to database, please try again later.</p>";
		}
	}
	elseif(isset($_POST['deleteid'])){
		echo <<<EOT
		<p>Are you sure you want to delete this post?</p>
		<form method="GET" action="deletepost.php">
			<input type="hidden" name="postid" value="{$_POST['deleteid']}" />
			<input type="submit" name="confirm" value="Delete" />
		</form>
		<a href='forumview.php'><button>No</button></a>
EOT;
	}
	else{
		echo "<p>No post specified.</p>";
	}