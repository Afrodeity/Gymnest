function registration() {
	var form = document.registration;
	//Validate Username
	re = /^\w+$/;
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
	//Validate Confirmation
	if (form.password.value != form.confirmation.value) {
		alert("Password does not match its confirmation");
		form.password.focus();
		return false;
	}
	return true;
}

function login() {
	var form = document.login;
	//Validate Username
	re = /^\w+$/;
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
	return true;
}