<?php
function createContentArea($withText = ""){
	$contentArea = "<input type='hidden' id='content' name='content' /><div id='postcontent' contenteditable>" . $withText . "</div><input type='button' name='post' id='post' value='Post' onclick='submitPost()' />";
	return $contentArea;
}