<!DOCTYPE HTML5>
<html>
<head>
	<link rel="stylesheet" href="css/base.css" />
	<link rel="stylesheet" href="css/forumview.css" />
</head>
<body>
<div id="topbar">
	<img src="img/header.png" />
</div>

<div id="breadcrumb">
<a href="index.php">Home</a> -> <a href="forumview.php">Forums</a>
</div>

<div class="maindiv">
<?php
session_start();
require_once("php/database.php");
require_once("php/storedprocedures.php");
require_once("php/error.php");

$db = connectToDatabase();
$threads;
$forums;

if(isset($_GET['forumid'])){
	$result = getForumInfo($db, $_GET['forumid']);
	$forumName = $result['ForumName']; //ParentForumID, ForumSubtitle, Topic
	$parentID = $result['ParentForumID'];
	echo "<h2 class='title'>$forumName</h2>";
	$threads = getForumThreads($db, $_GET['forumid']);
	$forums = getChildForums($db, $_GET['forumid']);
}
else{
	echo "<h2 class='title'>Home</h2>";
	$forums = getChildForums($db, null); //Gets top-level forums
}
// Get rid of error key so it won't interfere later
$forumError = $forums["Error"];
unset($forums["Error"]);
$threadError = $threads["Error"];
unset($threads["Error"]);

foreach($forums as $forum){
	$topics[] = $forum["Topic"];
}
$topics = array_unique($topics);

foreach($topics as $topic){
	echo "<div class='forumbox'><h2 class='title'>$topic</h2><ol>";
	foreach($forums as $forum){ //ID, name, subtitle, topic
		echo <<<EOT
<li>
	<div class='subitem'>
		<div class='threadmeta'>
			<p><a href='forumview.php?forumid={$forum['ForumID']}'>{$forum['ForumName']}</a></p>
			<p>{$forum['ForumSubtitle']}</p>
		</div>
		<div class='threadstats'>
			<p>Threads: ???</p>
			<p>Replies: ???</p>
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