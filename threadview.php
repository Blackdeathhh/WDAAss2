<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/forumview.css" />
	<link rel="stylesheet" href="css/threadview.css" />
</head>
<body>
<div id="topbar">
	<img src="img/header.png" />
</div>

<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

$db = connectToDatabase();
$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
$threadID;
$postsPerPage = 10;
$postIDs;

if(isset($_GET['threadid'])){
	$threadID = $_GET['threadid'];
}
else{
	echo "No thread.";
	exit;
}

if(isset($_SESSION['id']) && isset($_SESSION['token'])){
	$results = getPrivateUserDetails($db, $_SESSION['id'], $_SESSION['token']);
	$_SESSION['token'] = $results[SP::TOKEN];

	if($results[SP::ERROR] == ERR::OK){
		$postsPerPage = $results[USER::POSTS_PAGE];
	}
}

$postIDs = getThreadPosts($db, $_GET['threadid']);
$errorCode = $postIDs[SP::ERROR];
unset($postIDs[SP::ERROR]);
$numPosts = count($postIDs);

echo <<<EOT
<div id="breadcrumb">
<a href="index.php">Home</a> -> <a href="forumview.php">Forums</a>
</div>

<div class="maindiv">
EOT;

if($errorCode == ERR::OK && $numPosts  != 0){
	$numPages = intval($numPosts / $postsPerPage + 1);
	echo "<ol class='pages'>";
	for($i = 0; $i != $numPages; ++$i){
		if($i == $page){
			echo "<li class='curpage'>". ($i + 1) ."</li>";
		}else{
			echo "<a href='threadview.php?threadid=". $threadID ."&page=". $i ."'><li>". ($i + 1) ."</li></a>";
		}
	}
	echo "</ol>";
}
else{
	switch($errorCode){
		case ERR::OK:
			echo "There are no posts in this thread.";
			break;
		case ERR::UNKNOWN:
			echo "There was an issue obtaining post data. Please try again later.";
			break;
	}
}

//Intentional; we do stop 1 before $max, otherwise we'd return $postsPerPage + 1 posts.
$max = ($page * $postsPerPage) + $postsPerPage;
if($max > $numPosts) $max = $numPosts;
$postsToGet = array();
for($i = $page * $postsPerPage; $i != $max; ++$i){
	$postsToGet[] = $postIDs[$i][POST::ID];
}

try{
	$posts = multigetPostDetails($db, $postsToGet);
}
catch(RuntimeException $e){
	echo $e->getMessage();
}

$userDetails = array();

if($posts){
	echo "<ol>";
	foreach($posts as $ID => $details){
		switch($details[SP::ERROR]){
			case ERR::OK:
				$userID = $details[POST::USER_ID];
				if(!isset($userDetails[$userID])){
					// If it doesn't work this time it probably won't work the next time. We can change this to not output it if the error is unknown later, maybe
					$userDetails = getPublicUserDetails($db, $userID);
					$userDetails[$userID] = $details;
				}
				echo <<<EOT
<li class='item'>
	<div class='reply'>
		<div class='replyheader'>
			<p>Posted at {$details[POST::MADE_AT]}</p>
		</div>
		<div class='replybody'>
			<div class='posterinfo'>
EOT;
				echo <<<EOT
				<p>
					<a href='viewprofile.php?profileID={$details[POST::USER_ID]}'>{$userDetails[USER::DISP_NAME]}</a>
				</p>
				<img class='avatar' src='avatar/{$details[POST::USER_ID]}.jpg' alt="{$userDetails[USER::DISP_NAME]}'s avatar" />
				<p>Location: {$userDetails[USER::LOC]}</p>
			</div>
			<div class='content'>
				{$details[POST::CONTENT]}
			</div>
		</div>
		<div class='replyfooter'>
EOT;
				$editor = $details[POST::EDITING_USER_ID];
				if($editor){
					echo "<p>Last edited ";
					if($editor != $details[POST::USER_ID]){
						if(!isset($userDetails[$userID])){
							$details = getPublicUserDetails($db, $userID);
							$userDetails[$userID] = $details;
						}
						echo "by <a href='viewprofile?profileID=$editor'>". $userDetails[$editor][USER::DISP_NAME] . "</a> ";
					}
					echo "at " . $details[POST::EDITED_AT] . "</p>";
				}
				echo "</div></div></li>";
				break;
			case ERR::POST_NOT_EXIST:
				echo "Post does not exist, or has been deleted.";
				break;
			case ERR::UNKNOWN:
			default:
				echo "Unknown error obtaining post.";
				break;
		}
	}
	echo "</ol>";
}

echo "<a href='makepost.php?threadid=$threadID'>New Post</a></div>";
?>
<!--//maindiv
/*
	postIDs = getThreadPosts
	postsPerPage = getUserInfo
	posts = getPostInfo(arrayslice(min, max, postIDs))
	
	foreach post
		echo the div, li, etc. for that post
	
	-- If page is 0 and PostsPerPage 50, posts 0~49
	min: page x postsPerPage
	max: min + postsPerPage - 1

	We should query the db every time the page is reloaded. That way, we get information of new posts. Asking for the entire list of IDs is fine.
*/-->
</div>
</body>
</html>