<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/forumview.css" />
	<meta charset="UTF-8">
</head>
<body>
<?php
session_start();
require("php/topbar.php");
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

$db = connectToDatabase();
$threads;
$forums;
$curForumInfo = null;

if(isset($_GET['forumid'])){
	$curForumInfo = getForumInfo($db, $_GET['forumid']);
	$threads = getForumThreads($db, $_GET['forumid']);
	$forums = getChildForums($db, $_GET['forumid']);
}
else{
	$forums = getChildForums($db, null); //Gets top-level forums
}
// Get rid of error key so it won't interfere later
$forumError = $forums[SP::ERROR];
unset($forums[SP::ERROR]);
$threadError = $threads[SP::ERROR];
unset($threads[SP::ERROR]);

foreach($forums as $forum){
	$topics[] = $forum[FORUM::TOPIC];
}
$topics = array_unique($topics);

echo "<div id='breadcrumb'><a href='index.php'>Home</a> -> <a href='forumview.php'>Forums</a>";
if(isset($_GET['forumid'])){
	$ancestryIDs = getForumAncestry($db, $_GET['forumid']);
	$ancestryError = $ancestryIDs[SP::ERROR];
	unset($ancestryIDs[SP::ERROR]);
	$breadcrumbs = array();
	for($i = count($ancestryIDs) - 1; $i >= 0; --$i){
		$info = getForumInfo($db, $ancestryIDs[$i]);
		$breadcrumbs[] = "<a href='forumview.php?forumid=". $info[FORUM::ID] .">". $info[FORUM::NAME] ."</a>";
	}
	echo implode(" -> ", $breadcrumbs);
	echo " -> ". $curForumInfo[FORUM::NAME];
}
echo "</div><div class='maindiv'>";

if($curForumInfo && isset($_GET['forumid'])){
	echo <<<EOT
<h2 class='title'>{$curForumInfo[FORUM::NAME]}</h2>
<form method=POST action="makethread.php">
	<input type="hidden" name="forumid" value="{$_GET['forumid']}" />
	<input type="submit" value="New Thread" />
</form>
EOT;
}
else{
	echo "<h2 class='title'>Forums</h2>";
}

foreach($topics as $topic){
	echo "<div class='forumbox'><h2 class='title'>$topic</h2><ol>";
	foreach($forums as $forum){ //ID, name, subtitle, topic
		echo <<<EOT
<li>
	<div class='subitem'>
		<div class='threadmeta'>
			<p><a href='forumview.php?forumid={$forum[FORUM::ID]}'>{$forum[FORUM::NAME]}</a></p>
			<p>{$forum[FORUM::SUBTITLE]}</p>
		</div>
		<div class='threadstats'>
			<p>Threads: ???</p>
		</div>
		<div class='threadlastpost'>
			<p>Latest Thread: ???</p>
			<p>At ??:?? ??/??/??</p>
		</div>
	</div>
</li>
EOT;
	}
	echo "</ol></div>";
}

//ThreadID, StarterUserID, ThreadTitle, CreatedAt, isSticky
if(isset($threads)){
	echo "<div class='forumbox'><h2 class='title'>Threads</h2><ol>";

	foreach($threads as $thread){
		$user = getPublicUserDetails($db, $thread['StarterUserID']);
		echo <<<EOT
<li>
	<div class='subitem'>
		<div class='threadmeta'>
			<img class="threadicon" src="img/open.jpg" alt="Thread Open" />
			<p><a href='threadview.php?threadid={$thread['ThreadID']}&page=0'>{$thread['ThreadTitle']}</a></p>
			<p>Started by {$user['DisplayName']}, at {$thread['CreatedAt']}.</p>
		</div>
		<div class='threadstats'>
			<p>Views: ???</p>
			<p>Replies: ???</p>
		</div>
		<div class='threadlastpost'>
			<p>View ??? by ???</p>
			<p>At ??:?? ??/??/??</p>
		</div>
	</div>
</li>
EOT;
	echo "</ol>";
	}
}
?>
</div>
</body>
</html>