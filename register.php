<?php
include_once 'connect.php';
include_once 'config.php';

$error_msg = "";

if (isset($_POST['username'], $_POST['p'])) {
	//Input Sanitizarion
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
	
	$stmt = "SELECT userID FROM users WHERE username = ? LIMIT 1";
	$stmt = $mysqli->prepare($stmt);
	//check if username alread exists
	if ($stmt) {
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows == 1) { //user already exists
			$error_msg .= '<p class="error">Username taken. Please choose another.</p>';
		}
	} else {
		$error_msg .= '<p class="error">Error verifying registration. Please try again.</p>';
	}
	$stmt->close();
	
	if(empty($error_msg)) {
		$password = password_hash($password, PASSWORD_DEFAULT);
		//Insert new user to database
		if($insert_stmt = $mysqli->prepare("INSERT INTO users (username, password, salt) VALUES (?, ?, ?)")) {
			$insert_stmt->bind_param('sss', $username, $password, $salt);
			if(! $insert_stmt->execute()) {
				//$error_msg .= "<p class="error">Error creating user. Please try again.</p>";
				header('Location: ./error.php?err=Error creating user. Please try again.');
				exit;
			}
		}
	//Redirect to dashboard upon success
	header('Location: ./registered.php');
	exit;
	}
}