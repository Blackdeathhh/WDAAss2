function validate(which) {
	/*var validateUsername = false;
	var validateDisplayname = false;
	var validatePassword = false;*/
	var errorLabel = document.getElementById("errormessage");
	var usernameValid = false;
	var displaynameValid = false;
	var passwordValid = false;
	
	/*if(which == undefined) {
		validateUsername = validateDisplayname = validatePassword = true;
	}
	else {
		if(which == document.getElementById("username")) {
			validateUsername = true;
		}
		if(which == document.getElementById("displayname")) {
			validateDisplayname = true;
		}
		if(which == document.getElementById("password")) {
			validatePassword = true;
		}
	}
	if(validateUsername) {*/
		var label = document.getElementById("usernameerror");
		var errMsg = usernameErrors();
		if(errMsg == "") {
			label.className = "errorhide";
			usernameValid = true;
		}
		else {
			label.className = "errorshow";
			label.innerHTML = errMsg;
		}
	/*}
	if(validateDisplayname) {*/
		var label = document.getElementById("displaynameerror");
		var errMsg = displaynameErrors();
		if(errMsg == "") {
			label.className = "errorhide";
			displaynameValid = true;
		}
		else {
			label.className = "errorshow";
			label.innerHTML = errMsg;
		}
	/*}
	if(validatePassword) {*/
		var label = document.getElementById("passworderror");
		var errMsg = passwordErrors();
		if(errMsg == "") {
			label.className = "errorhide";
			passwordValid = true;
		}
		else {
			label.className = "errorshow";
			label.innerHTML = errMsg;
		}
	//}
	document.getElementById("submit").disabled = !(usernameValid && displaynameValid && passwordValid);
}

function usernameErrors() {
	var username = document.getElementById("username").value;
	if(username.length > 20) {
		return "Username can be at most 20 characters";
	}
	if(username.length == 0) {
		return "Please enter a username";
	}
	return "";
}

function displaynameErrors() {
	var displayname = document.getElementById("displayname").value;
	if(displayname.length > 20) {
		return "Display Name can be at most 20 characters";
	}
	if(displayname.length == 0) {
		return "Please enter a display name";
	}
	return "";
}

function passwordErrors() {
	var password = document.getElementById("password").value;
	if(password.length > 60) {
		return "Password can be at most 60 characters";
	}
	if(password.length == 0) {
		return "Please enter a password";
	}
	return "";
}