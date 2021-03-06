<?php
	include_once 'config.php';
	
	function start_session() {
		$session_name = 'session';
		$secure = true;
		$httponly = true; //block JavaScript session id access
		/*
		if (ini_set('session.use_only_cookies', 1) == FALSE) {
			echo "Could not establish a safe session.";
			exit();
		}
		*/
		//Get current cookie parameters
		$cookie_params = session_get_cookie_params();
		session_set_cookie_params(
			$cookie_params["lifetime"],
			$cookie_params["path"],
			$cookie_params["domain"],
			$secure,
			$httponly
		);
		session_name($session_name);
		session_start();
		session_regenerate_id(true);
	}
	
	function end_session() {
		session_start();
		unset($_SESSION['username']);
		unset($_SESSION['password']);
	}
	
	function login($username, $password, $mysqli) {
		if($stmt = $mysqli->prepare("SELECT userID, username, password FROM users WHERE username = ? LIMIT 1")) {
			$stmt->bind_param('s', $username); //bind $username as string(s)
			$stmt->execute();
			$stmt->store_result();
			
			$stmt->bind_result($userID, $username, $correct);
			$stmt->fetch(); //retrieve bound variables and assign to bind
			$password = password_hash($password, PASSWORD_DEFAULT);
			
			if($stmt->num_rows == 1) {
				if(checkbrute($userID, $mysqli) == false) {
					if(password_verify($password, $hash)) {
						//XSS protection - hide id, hash login_string
						$userID = preg_replace("/[^0-9]+/", "", $userID);
						$_SESSION['userID'] = $userID;
						$username = preg_replace("/[a-zA-Z0-9_\-]+/",  "", $username);
						$_SESSION['username'] = $username;
						return true;
					} //wrong password
				} else { //record failed attempt
                    $now = time();
                    $mysqli->query("INSERT INTO logins(userFK, time) VALUES ('$userID', '$now')");
                }
			} //user doesn't exist
		} //syntactical error
		return false;
	}
	
	function check_brute($userID, $mysqli) {
		$now = time();
		$valid_attempts = $now - (2*60*60);
		if ($stmt = $mysqli->prepare("SELECT time FROM logins WHERE time > '$valid_attemts'")) {
			$stmt->bind_param('i', $userID);
			$stmt->execute();
			$stmt->store_result();
			//Number of allowed attempts: 5
			if ($stmt->num_rows > 5) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	function check_login($mysqli) { //login string == password
		if (isset($_SESSION['userID'], $_SESSION['username'], $_SESSION['login_string'])) {
			$userID = $_SESSION['userID'];
			$login_string = $_SESSION['login_string'];
			$username = $_SESSION['username'];
			//retrieve password
			if($stmt = $mysqli->prepare("SELECT password FROM users WHERE userID = ? LIMIT 1")) {
				$stmt->bind_param('i', $user_id);
				$stmt->execute();
				$stmt->store_result();
				//process query
				if ($stmt->num_rows == 1) { // If the user exists
					$stmt->bind_result($password);
					$stmt->fetch();
					//validate password
					if ($login_string == password_hash($password, DEFAULT_PASSWORD)) {
						return true; // Logged In
		}	}	}	}
		return false; //Not Logged in
	}
	
	function esc_url($url) {
		if ('' == $url) {
			return $url;
		}
		$url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
		$strip = array('%0d', '%0a', '%0D', '%0A');
		$url = (string)$url;
	 
		$count = 1;
		while ($count) {
			$url = str_replace($strip, '', $url, $count);
		}
	 
		$url = str_replace(';//', '://', $url);
	 
		$url = htmlentities($url);
	 
		$url = str_replace('&amp;', '&#038;', $url);
		$url = str_replace("'", '&#039;', $url);
	 
		if ($url[0] !== '/') {
			return '';
		} else {
			return $url;
		}
	}
	
	function get_array($query) {
		$rows = array();
		$result = mysqli_query($mysqli, $query);
		while ($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
		return $rows;
	}