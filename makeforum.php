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

// Handle error messages from formsubmission.php

	echo <<<EOT
	<ol>
		<form method=POST action="forumsubmission.php">
			<input type="hidden" name="parentid" value="{$_POST['parentid']}" />
			<li><label>Forum Name:</label><input type="text" name="name" id="name" /></li>
			<li><label>Forum Subtitle:</label><input type="text" name="subtitle" id="subtitle" /></li>
			<li><label>Forum Topic:</label><input type="text" name="topic" id="topic" /></li>
			<li><input type="submit" id="submit" value="Submit" /></li>
		</form>
	</ol>
EOT;
?>
</div>
</body>
</html>