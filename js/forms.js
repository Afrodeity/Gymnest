function registration() {
	var form = document.registration
	//Validate Username
	var re = /^\w+$/;
	var username = form.username;
	if(!re.test(username.value)) {
		alert("Username may only contain letters, numbers and underscores");
		username.focus();
		return false;
	}
	//Validate Password
	var password = form.password;
	if (password.value.length < 6) {
		alert("Password must at least contain 6 characters");
		password.focus();
		return false;
	}
	//Validate Confirmation
	if (password.value != form.confirmation.value) {
		alert("Password does not match its confirmation");
		password.focus();
		return false;
	}
}

function login() {
	var form = document.login;
	//Validate Username
	var re = /^\w+$/;
	if(!re.test(form.username.value)) {
		alert("Username may only contain letters, numbers and underscores");
		form.username.focus();
		return false;
	}
	//Validate Password
	if (form.password.value.length < 6) {
		alert("Password must at least contain 6 characters");
		form.password.focus();
		return false;
	}
}