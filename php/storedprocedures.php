<?php

function getSalt($database, $username) {
	$stmt = $db->prepare("CALL GetSalt(:user)");
	$stmt->bindParam(":user", $username, PDO::PARAM_STR);
	try{
		$stmt->execute();
	}
	catch(PDOException $e){
		echo $e->getMessage() . "<br />";
	}
	// Table's just one row, one column.
	$salt = $stmt->fetchAll()[0]["Salt"];
	$stmt->closeCursor();
	return $salt;
}