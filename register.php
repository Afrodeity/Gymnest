<?php
include_once 'connect.php';
include_once 'config.php';

$error_msg = "";

if (isset($_POST['username'], $_POST['password'])) {
	//Input Sanitizarion
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
	$stmt = $mysqli->prepare("SELECT userID FROM users WHERE username = ? LIMIT 1");
	//check if username alread exists
	if ($stmt) {
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 1) { //user already exists
			echo "Username already exists. Returning to the GymNest portal.";
			header("Refresh: 3; url=portal.html");
			exit;
		}
	} else {
		echo "Failed to establish database connection. Returning to the GymNest portal.";
		header("Refresh: 3; url=portal.html");
		exit;
	}
	$stmt->close();

	$password = password_hash($password, PASSWORD_DEFAULT);
	//Insert new user to database
	if($insert_stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)")) {
		$insert_stmt->bind_param('ss', $username, $password);
		if(! $insert_stmt->execute()) {
			//$error_msg .= "<p class="error">Error creating user. Please try again.</p>";
			echo "Error creating user. Please try again. Returning to the GymNest portal.";
			header("Refresh: 3; url=portal.html");
			exit;
		}
	}
	//Redirect to dashboard upon success
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: http://$host$uri/dashboard/dashboard.html");
	exit;
}