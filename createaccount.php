<?php
	/*
	We have to ensure that the fields are not too long.
	Then we have to call a stored procedure. If it works, log the user in and send them to their profile, with a parameter "Firsttime" or something, to greet them. 
	If it doesn't, we have to go back to register.php with some information as to what they did wrong.
	Possible errors are returned as strings from the SProc, so an empty string can be considered a success. If we don't get an empty string, we just have to go back to register.php, giving them that string to tell them why they have failed to make an account.
	*/
	require_once("php/database.php");
	//require_once("php/validation.php");
	require_once("php/security.php");
	
	$db = connectToDatabase();
	
	if($db) {
		$username = $_POST["username"];
		$displayname = $_POST["displayname"];
		$rawPassword = $_POST["password"];
		
		/*Validate parameters, make sure they're not too long.
		validateUsername();
		validateDisplayname();
		validatePassword();
		*/
		
		$hashedPass = hashPassword($rawPassword);
		
		//echo "Password: $hashedPass. ";
		
		// Note that binding an OUT parameter does not work. Therefore, user variables are used as a workaround.
		//$errorMessage = "";
		//$stmt = $db->prepare("CALL RegisterUser(:user, :pass, :display, :error)");
		//$stmt->bindParam(":error", $errorMessage, PDO::PARAM_STR, 50);
		$stmt = $db->prepare("CALL RegisterUser(:user, :pass, :display, @error)");
		$stmt->bindParam(":user", $username, PDO::PARAM_STR);
		$stmt->bindParam(":pass", $hashedPass, PDO::PARAM_STR);
		$stmt->bindParam(":display", $displayname, PDO::PARAM_STR);
		$success = false;
		try{
			$stmt->execute();
			$success = true;
		}
		catch(PDOException $e){
			echo $e->getMessage();
		}
		$outParams = $db->query("SELECT @error")->fetch(PDO::FETCH_ASSOC)
		echo $outParams['@error'];
		//echo "Executed. Result: $errorMessage";
		if($success) {
			//echo "Success.";
			// It worked, try to login.
			$stmt = $db->prepare("CALL LoginUser(:user, :hash, :error)");
		}
		else {
			// Error, dang.
			//echo "Failure.";
		}
	}
	else {
		// Failed to connect, awww shit.
		//header("Location: register.php");
		//echo "Failed to connect";
	}