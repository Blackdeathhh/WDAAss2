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
		var usernameLabel = document.getElementById("usernameerror");
		var errMsg = usernameErrors();
		if(errMsg == "") {
			usernameLabel.className = "errorhide";
			usernameValid = true;
		}
		else {
			usernameLabel.className = "errorshow";
			usernameLabel.innerHTML = errMsg;
		}
	/*}
	if(validateDisplayname) {*/
		var displaynameLabel = document.getElementById("displaynameerror");
		var errMsg = displaynameErrors();
		if(errMsg == "") {
			displaynameLabel.className = "errorhide";
			displaynameValid = true;
		}
		else {
			displaynameLabel.className = "errorshow";
			displaynameLabel.innerHTML = errMsg;
		}
	/*}
	if(validatePassword) {*/
		var passwordLabel = document.getElementById("passworderror");
		var errMsg = passwordErrors();
		if(errMsg == "") {
			passwordLabel.className = "errorhide";
			passwordValid = true;
		}
		else {
			passwordLabel.className = "errorshow";
			passwordLabel.innerHTML = errMsg;
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
	if(/^[!-~]{1,20}$/.test(username)){
		return "Username must contain no spaces";
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
	if(/^[!-~]{1,20}$/.test(displayname)){
		return "Username must contain no spaces";
	}
	return "";
}

function passwordErrors() {
	var password = document.getElementById("password").value;
	if(password.length > 72) {
		return "Password can be at most 72 characters";
	}
	if(password.length == 0) {
		return "Please enter a password";
	}
	if(/^.{1,72}$/.test(password)){
		return "Username must contain no spaces";
	}
	return "";
}