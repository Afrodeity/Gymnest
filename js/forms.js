function registration(form, username, password, confirm) {
	//Probably not required------------------------------------------------
	//Validate Username
	re = /^\w+$/;
	if(!re.test(form.username.value)) {
		alert("Username may only contain letters, numbers and underscores");
		form.username.focus();
		return false;
	}
	//Validate Password
	if (password.value.length < 6) {
		alert("Password must at least contain 6 characters");
		form.password.focus();
		return false;
	}
	//Validate Confirmation
	if (password.value != confirmation.value) {
		alert("Password does not match its confirmation");
		form.password.focus();
		return false;
	}
	//--------------------------------------------------------------------
	//Input Element to hold the hashed password
	var hiddenPassword = document.createElement("input");
	form.appendChild(hiddenPassword);
	hiddenPassword.name = "p";
	hiddenPassword.type = "hidden";
	hiddenPassword.value = hex_sha512(password.value);
	//Empty the plaintext password (Interception Countermeasure)
	password.value = "";
	confirmation.value = "";
	
	//form.submit();
	return true;
}

function login(form, password) {
	var p = document.createElement("input");
	form.appendChild(p);
	p.name = "p";
	p.type = "hidden";
	p.value = hey_sha512(password.value);
	password.value = "";
	form.submit();
}