<?php
	include_once 'db_connect.php';
	include_once 'functions.php';
	 
	start_session();
	//p - encrypted password 
	if (isset($_POST['username'], $_POST['p'])) {
		$username = $_POST['username'];
		$password = $_POST['p'];
	 
		if (login($username, $password, $mysqli) == true) {
			header('Location: ../protected_page.php'); // Correct inputs
		} else {
			header('Location: ../index.php?error=1'); // Incorrect inputs
		}
		exit;
	} else { 
		echo 'Invalid Request'; // Wrong or missing inputs
	}
?>