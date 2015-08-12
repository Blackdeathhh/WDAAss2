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
			<p><a href='threadview.php?threadid={$thread['ThreadID']}'>{$thread['ThreadTitle']}</a></p>
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
	<!--<h2 class="title">Home</h2>
	<div class="forumbox">
		<h2 class="title">Subforums</h2>
		<ol>
		<li>
			<div class="subitem">
				<div class="threadmeta">
					<p>Subforum A</p>
				</div>
				<div class="threadstats">
					<p>Threads: 2</p>
					<p>Replies: 22h</p>
				</div>
				<div class="threadlastpost">
					<p>heyyyyy</p>
					<p>heyyyyy</p> 
				</div>
			</div>
		</li>
		<li>
			<div class="subitem">
				Subforum B
			</div>
		</li>
		<li>
			<div class="subitem">
				Subforum Dick
			</div>
		</li>
		</ol>
	</div>

	<div class="forumbox">
		<h2 class="title">Threads</h2>
		<ol>
		<li>
			<div class="subitem">
				<div class="threadmeta">
					<img class="threadicon" src="img/open.png" alt="Thread Open" />
					<p><a href="threadviewmockup.html">Hello World!</a></p>
					<p>Started by <a href="profileview.php?thisguy">Tom</a>, at 11:37 26/07/2015.</p>
				</div>
				<div class="threadstats">
					<p>Views: 3</p>
					<p>Replies: 2</p>
				</div>
				<div class="threadlastpost">
					<p>View <a href="threadviewmockup.html#lastreply">last post</a> by <a href="profileview.php?thisguy">Ruby</a></p>
					<p>At 12:00 26/07/2015.</p>
				</div>
			</div>
		</li>
		<li>
			<div class="subitem">
				<div class="threadmeta">
					<img class="threadicon" src="img/locked.png" alt="Thread Locked" />
					<p><a href="threadviewmockup.html">I might have to bite out your throat.</a></p>
					<p>Started by <a href="profileview.php?thisguy">Red</a>, at 10:37 26/07/2015.</p>
				</div>
				<div class="threadstats">
					<p>Views: 40</p>
					<p>Replies: 2</p>
				</div>
				<div class="threadlastpost">
					<p>View <a href="threadviewmockup.html#lastreply">last post</a> by <a href="profileview.php?thisguy">Some Mod</a></p>
					<p>At 12:00 26/07/2015.</p>
				</div>
			</div>
		</li>
		</ol>
	</div>-->
</div>
</body>
</html>