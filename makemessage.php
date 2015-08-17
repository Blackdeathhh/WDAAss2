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
	
	if(isset($_GET['touserid'])){
		echo <<<EOT
<h2 class='title'>Creating a new message to: <a href=profile.php?profileid={$_GET['touserid']}>{$GETUSERDISPLAYNAME}</a></h2>
<form id="postform" method="POST" action="messagesubmission.php">
	<input type="text" name="messagetitle" id="messagetitle" />
	<input type='hidden' id='touserid' name='touserid' value='{$_GET['touserid']}'>
EOT;
		echo createContentArea();
		echo "</form>";
	}
	else{
		echo "<p>No recipient specified.</p>";
	}
?>
		<noscript>Javascript is required to make a message.</noscript>
	</div>
</body>
</html>