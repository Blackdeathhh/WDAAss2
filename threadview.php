<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/forumview.css" />
	<link rel="stylesheet" href="css/threadview.css" />
	<meta charset="UTF-8">
</head>
<body>
<?php
/* We have to somehow deduce the first viewing of this thread, and ++views. We could do this by checking page GET.
Or, we could make a new GET variable, just for telling if we're browsing through this or not. We'd check to see if it's set; if not, ++View. And in the page buttons, just make it submit the GET parameter.
*/
session_start();
require("php/topbar.php");
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

$db = connectToDatabase();
$page = (isset($_GET['page'])) ? $_GET['page'] : 0;
$threadID;
$threadInfo;
$postsPerPage = 10;
$focusPostID = (isset($_GET['postid'])) ? $_GET['postid'] : null;
$postIDs;

if(isset($_GET['threadid'])){
	$threadID = $_GET['threadid'];
	$threadInfo = getThreadInfo($db, $threadID);
}
else{
	echo "No thread.";
	exit;
}

if(isset($_SESSION['id']) && isset($_SESSION['token'])){
	$results = getPrivateUserDetails($db, $_SESSION['id'], $_SESSION['token']);
	//$_SESSION['token'] = $results[SP::TOKEN];

	if($results[SP::ERROR] == ERR::OK){
		$postsPerPage = $results[USER::POSTS_PAGE];
	}
}

$postIDs = getThreadPosts($db, $_GET['threadid']);
$errorCode = $postIDs[SP::ERROR];
unset($postIDs[SP::ERROR]);
$numPosts = count($postIDs);

$parentForumInfo = getForumInfo($db, $threadInfo[THREAD::FORUM_ID]);

$crumbs = array();
$crumbs[] = "<a href='index.php'>Home</a>";
$crumbs[] = "<a href='forumview.php'>Forums</a>";
$crumbs[] = "<a href='forumview.php?forumid=". $parentForumInfo[FORUM::ID] .">". $parentForumInfo[FORUM::NAME] ."</a>"

if(isset($threadInfo)){
	$ancestryIDs = getForumAncestry($db, $threadInfo[THREAD::FORUM_ID]);
	$ancestryError = $ancestryIDs[SP::ERROR];
	unset($ancestryIDs[SP::ERROR]);
	for($i = count($ancestryIDs) - 1; $i >= 0; --$i){
		$info = getForumInfo($db, $ancestryIDs[$i]);
		$crumbs[] = "<a href='forumview.php?forumid=". $info[FORUM::ID] .">". $info[FORUM::NAME] ."</a>";
	}
}

$crumbs[] = $threadInfo[THREAD::TITLE];
$breadcrumb = implode(" -> ", $crumbs);

echo <<<EOT
<div id="breadcrumb">
{$breadcrumb}
</div>

<div class="maindiv">
<h2 class='title'>{$threadInfo[THREAD::TITLE]}</h2>
EOT;

if($errorCode == ERR::OK && $numPosts  != 0){
	$numPages = intval($numPosts / $postsPerPage + 1);
	if($page > $numPages) $page = $numPages - 1;
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

$max = ($page * $postsPerPage) + $postsPerPage;
if($max > $numPosts) $max = $numPosts;
$postsToGet = array();
//Intentional; we do stop 1 before $max, otherwise we'd return $postsPerPage + 1 posts.
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
	<div id='post{$ID}' class='reply'>
		<div class='replyheader'>
			<p>Posted at {$details[POST::MADE_AT]}</p>
		</div>
		<div class='replybody'>
			<div class='posterinfo'>
EOT;
				echo <<<EOT
				<p>
					<a href='profile.php?profileid={$details[POST::USER_ID]}'>{$userDetails[USER::DISP_NAME]}</a>
				</p>
				<img class='avatar' src='avatar/{$details[POST::USER_ID]}.jpg' alt="{$userDetails[USER::DISP_NAME]}'s avatar" />
				<p>Location: {$userDetails[USER::LOC]}</p>
			</div>
			<div class='content'>
				<p>{$details[POST::CONTENT]}</p>
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
						echo "by <a href='viewprofile?profileid=$editor'>". $userDetails[$editor][USER::DISP_NAME] . "</a> ";
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

echo <<<EOT
<form method="post" action="makepost.php">
	<input type="hidden" name="threadid" value="{$_GET['threadid']}" />
	<input type="submit" value="New Post" />
</form>
EOT;
?>
</div>
</body>
</html>