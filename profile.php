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
	require_once("php/error.php");

	if(isset($_POST['submit'])){
		echo "POST Set; modifying details...";
		//$db = connectToDatabase();
		//$results = modifyUserDetails($db);
		
	}

	if($_GET['profileID']){
		$db = connectToDatabase();
		$results = getPublicUserDetails($db, $_GET['profileID']);
		if($results['error'] == ERR::OK){
		echo <<<EOT
<li><label>Display Name: </label>{$results['displayName']}</li>
<li><label>Location: </label>{$results['location']}</li>
<li><label>Gender: </label>{$results['gender']}</li>
EOT;
		}
		else {
			echo "Error: {$results['error']}.";
		}
	}
	elseif($_SESSION['id']){
		$db = connectToDatabase();
		$results = getPrivateUserDetails($db, $_SESSION['id'], $_SESSION['token']);
		$_SESSION['token'] = $results['token'];
		if($results['error'] == ERR::OK){
		echo <<<EOT
<li><label>[DEBUG]Current Token: </label>{$_SESSION['token']}</li>
<li><label>Display Name: </label>{$results['displayName']}</li>
<form method="POST" action="profile.php">
	<li><label>Location: </label><input type="text" name="location" id="location">{$results['location']}</input></li>
	<li><label>Email: </label><input type="text" name="email" id="email">{$results['email']}</input></li>
	<li><label>Gender: </label><input type="text" name="gender" id="gender">{$results['gender']}</input></li>
	<li><label>Posts per Page: </label><input type="text" name="postsPerPage" id="postsPerPage">{$results['postsPerPage']}</input></li>
	<li><input type="submit" id="submit" name="submit" value="Modify"></input></li>
</form>
EOT;
		}
		else {
			echo "Error: {$results['error']}";
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