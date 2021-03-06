<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/forumview.css" />
	<meta charset="UTF-8">
<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");
require_once("php/constants.php");

$db = connectToDatabase();
$threads;
$forums;
$curForumInfo = null;

if(isset($_GET['forumid'])){
	$curForumInfo = getForumInfo($db, $_SESSION['id'], $_GET['forumid']);
	$threads = getForumThreads($db, $_SESSION['id'], $_GET['forumid']);
	$forums = getChildForums($db, $_SESSION['id'], $_GET['forumid']);
	echo "<title>" . $curForumInfo[FORUM::NAME] ."</title>";
}
else{
	$forums = getChildForums($db, $_SESSION['id'], null); //Gets top-level forums
	echo "<title>Forums</title>";
}
echo "</head><body>";

require("php/topbar.php");

// Get rid of error key so it won't interfere later
$forumError = $forums[SP::ERROR];
unset($forums[SP::ERROR]);
$threadError = $threads[SP::ERROR];
unset($threads[SP::ERROR]);

foreach($forums as $forum){
	$topics[] = $forum[FORUM::TOPIC];
}
$topics = array_unique($topics);

$crumbs = array();
$crumbs[] = "<a href='index.php'>Home</a>";
$crumbs[] = "<a href='forumview.php'>Forums</a>";

if(isset($_GET['forumid'])){
	$ancestryIDs = getForumAncestry($db, $_GET['forumid']);
	$ancestryError = $ancestryIDs[SP::ERROR];
	unset($ancestryIDs[SP::ERROR]);
	for($i = count($ancestryIDs) - 1; $i >= 0; --$i){
		$info = getForumInfo($db, $_SESSION['id'], $ancestryIDs[$i]);
		$crumbs[] = "<a href='forumview.php?forumid=". $info[FORUM::ID] ."'>". $info[FORUM::NAME] ."</a>";
	}
}
if($curForumInfo != null) $crumbs[] = $curForumInfo[FORUM::NAME];
$breadcrumb = implode(" -> ", $crumbs);

echo <<<EOT
<div id='breadcrumb'>
{$breadcrumb}
</div><div class='maindiv'>
EOT;

if($curForumInfo && isset($_GET['forumid'])){
	echo "<h2 class='title'>". $curForumInfo[FORUM::NAME] ."</h2>";
	if($_SESSION['permission'] >= $curForumInfo[FORUM::REQUIRED_LEVEL]){
		echo <<<EOT
<form method=POST action="makethread.php">
	<input type="hidden" name="forumid" value="{$_GET['forumid']}" />
	<input type="submit" value="New Thread" />
</form>
EOT;
	}
	else{
		echo "<button disabled>Can't Make Thread - Insufficient Permissions</button>";
	}
}
else{
	echo "<h2 class='title'>Forums</h2>";
}

// Don't bother telling them about insufficient permissions for this button
if($_SESSION['permission'] >= P_MAKE_FORUM){
	echo <<<EOT
	<form method=POST action="makeforum.php">
		<input type="hidden" name="parentid" value=
EOT;
	if($curForumInfo != null){
		echo "'". $curForumInfo[FORUM::ID] ."' />";
	}
	else{
		echo "'' />";
	}
	echo <<<EOT
		<input type="submit" value="New Forum" />
	</form>
EOT;
}

foreach($topics as $topic){
	echo "<div class='forumbox'><h2 class='title'>$topic</h2><ol>";
	foreach($forums as $forum){ //ID, name, subtitle, topic
		if($forum[FORUM::TOPIC] == $topic){
			echo <<<EOT
<li>
	<div class='subitem'>
		<div class='threadmeta'>
			<p><a href='forumview.php?forumid={$forum[FORUM::ID]}'>{$forum[FORUM::NAME]}</a></p>
			<p>{$forum[FORUM::SUBTITLE]}</p>
		</div>
		<div class='threadstats'>
			<p>Threads: {$forum[AGGR::NUM_THREADS]}</p>
		</div>
		<div class='threadlastpost'>
EOT;
			if(isset($forum[THREAD::ID]) && isset($forum[THREAD::TITLE]) && isset($forum[THREAD::MADE_AT])){
				echo <<<EOT
			<p>Latest Thread: <a href='threadview.php?threadid={$forum[THREAD::ID]}'>{$forum[THREAD::TITLE]}</a></p>
			<p>Created at {$forum[THREAD::MADE_AT]}</p>
EOT;
			}
			else{
				echo "<p>Latest Thread: None</p><p>Feels lonely...make a thread here!</p>";
			}
			echo <<<EOT
		</div>
	</div>
</li>
EOT;
		}
	}
	echo "</ol></div>";
}

//ThreadID, StarterUserID, ThreadTitle, CreatedAt, isSticky, Open, Views, Count(aggregate posts)
$userInfo = array();
if(isset($threads)){
	echo "<div class='forumbox'><h2 class='title'>Threads</h2><ol>";
	foreach($threads as $thread){
		if(!isset($userInfo[$thread[THREAD::STARTER_USER_ID]])){
			$userInfo[$thread[THREAD::STARTER_USER_ID]] = getPublicUserDetails($db, $thread[THREAD::STARTER_USER_ID]);
		}
		if(!isset($userInfo[$thread[POST::USER_ID]])){
			$userInfo[$thread[POST::USER_ID]] = getPublicUserDetails($db, $thread[POST::USER_ID]);
		}
		$threadStarter = $userInfo[$thread[THREAD::STARTER_USER_ID]];
		$latestPoster = $userInfo[$thread[POST::USER_ID]];
		echo <<<EOT
<li>
	<div class='subitem'>
		<div class='threadmeta'>
EOT;
		echo "<img class='threadicon' ";
		if($thread[THREAD::OPEN]){
			echo "src='img/open.jpg' alt='Thread Open' />";
		}
		else{
			echo "src='img/closed.jpg' alt='Thread Closed' />";
		}
		echo <<<EOT
			<p><a href='threadview.php?threadid={$thread[THREAD::ID]}'>{$thread[THREAD::TITLE]}</a></p>
			<p>Started by <a href='profile.php?profileid={$thread[THREAD::STARTER_USER_ID]}'>{$threadStarter[USER::DISP_NAME]}</a>, at {$thread[THREAD::MADE_AT]}.</p>
		</div>
		<div class='threadstats'>
			<p>Views: {$thread[THREAD::VIEWS]}</p>
			<p>Replies: {$thread[AGGR::NUM_POSTS]}</p>
		</div>
		<div class='threadlastpost'>
			<p>View <a href='threadview.php?threadid={$thread[THREAD::ID]}&postid={$thread[POST::LATEST_POST_ID]}'>latest post</a> by <a href='profile.php?profileid={$thread[POST::USER_ID]}'>{$latestPoster[USER::DISP_NAME]}</a></p>
			<p>At {$thread[THREAD::LATEST_POST_AT]}</p>
		</div>
	</div>
</li>
EOT;
	}
	echo "</ol>";
}
?>
	</div>
</div>
</body>
</html>