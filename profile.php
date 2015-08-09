<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
</head>
<body>
<div id="topbar">
	<img src="img/header.png" />
</div>

<div class="maindiv">
	<ol>
<?php
	session_start();
	require_once("php/database.php");
	require_once("php/storedprocedures.php");

	if($_GET['profileID']){
		$db = connectToDatabase();
		$results = getPublicUserDetails($db, $_GET['profileID']);
		if(!($results['error'])){
		echo <<<EOT
<li><label>Display Name: </label>$results['displayName']</li>
<li><label>Location: </label>$results['location']</li>
<li><label>Gender: </label>$results['gender']</li>
EOT;
		}
		else {
			echo "Error: $results['error'].";
		}
	}
	elseif($_SESSION['id']) {
		$db = connectToDatabase();
		$results = getPrivateUserDetails($db, $_SESSION['id']);
		$_SESSION['token'] = $results['token'];
		if(!($results['error'])){
		echo <<<EOT
<li><label>Current Token: </label>$_SESSION['token']</li>
<li><label>Display Name: </label>$results['displayName']</li>
<li><label>Location: </label>$results['location']</li>
<li><label>Email: </label>$results['email']</li>
<li><label>Gender: </label>$results['gender']</li>
<li><label>Posts per Page: </label>$results['postsPerPage']</li>
EOT;
		}
		else {
			echo "Error: $results['error'].";
		}
	}
	else{
		echo "Please log in or select a profile to view.";
	}
?>
	</ol>
</div>
</body>
</html>