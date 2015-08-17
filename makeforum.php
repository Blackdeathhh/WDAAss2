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
			<li><label><input type="text" name="name" id="name" /></label></li>
			<li><label><input type="text" name="subtitle" id="subtitle" /></label></li>
			<li><label><input type="text" name="topic" id="topic" /></label></li>
			<li><input type="submit" id="submit" value="Submit" /></li>
		</form>
	</ol>
EOT;
?>
</div>
</body>
</html>