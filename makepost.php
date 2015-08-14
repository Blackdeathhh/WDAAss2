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
		<form id="postform" method="POST" action="postsubmission.php">
			<input type="hidden" id="content" name="content" />
<?php
	require_once("php/database.php");
	require_once("php/storedprocedures.php");
	require_once("php/error.php");

	if(isset($_POST['threadid'])){
		echo "<input type='hidden' id='threadid' name='threadid' value='". $_POST['threadid'] ."'>";
	}
	if(isset($_POST['editID'])){

	}
	elseif(isset($_POST['editstr'])){

	}
?>
			<div id="postcontent" contenteditable>
			</div>
			<input type="button" name="post" id="post" value="Post" onclick="submitPost()" />
		</form>
		<noscript>Javascript is required to make a post.</noscript>
	</div>
</body>
</html>