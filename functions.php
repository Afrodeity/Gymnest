<?php
	include_once 'config.php';
	
	function start_session() {
		$session_name = 'session';
		$secure = true;
		$httponly = true; //block JavaScript session id access
		
		if (ini_set('session.use_only_cookies', 1) == FALSE) {
			header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
			exit();
		}
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
	
	function login($username, $password, $mysqli) {
		if($stmt = $mysqli->prepare("SELECT userID, username, password, salt FROM users WHERE username = ? LIMIT 1")) {
			$stmt->bind_param('s', $username); //bind $username as string(s)
			$stmt->execute();
			$stmt->store_result();
			
			$stmt->bind_result($userID, $username, $correct, $salt);
			$stmt->fetch(); //retrieve bound variables and assign to bind
			$password = hash('sha512', $password . $salt);
			
			if($stmt->num_rows == 1) {
				if(checkbrute($userID, $mysqli) == false) {
					if($password == $correct) {
						//XSS protection - hide id, hash login_string
						$user_browser = $_SERVER['HTTP_USER_AGENT'];
						$userID = preg_replace("/[^0-9]+/", "", $userID);
						$_SESSION['userID'] = $userID;
						$username = preg_replace("/[a-zA-Z0-9_\-]+/",  "", $username);
						$_SESSION['username'] = $username;
						$_SESSION['login_string'] = hash('sha512', $password . $user_browser);
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
	
	function check_login($mysqli) {
		if (isset($_SESSION['userID'], $_SESSION['username'], $_SESSION['login_string'])) {
			$userID = $_SESSION['userID'];
			$login_string = $_SESSION['login_string'];
			$username = $_SESSION['username'];
			$user_browser = $_SERVER['HTTP_USER_AGENT'];
			
			if($stmt = $mysqli->prepare("SELECT password FROM users WHERE userID = ? LIMIT 1")) {
				$stmt->bind_param('i', $user_id);
				$stmt->execute();
				$stmt->store_result();
	 
				if ($stmt->num_rows == 1) { // If the user exists
					$stmt->bind_result($password);
					$stmt->fetch();
					$login_check = hash('sha512', $password . $user_browser);
	 
					if ($login_check == $login_string) {
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