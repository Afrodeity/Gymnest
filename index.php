<html>
<head>
<Title>Gymnest Registration</Title>
<style type="text/css">
    body { background-color: #fff; border-top: solid 10px #000;
        color: #333; font-size: .85em; margin: 20; padding: 20;
        font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
    }
    h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
    h1 { font-size: 2em; }
    h2 { font-size: 1.75em; }
    h3 { font-size: 1.2em; }
    table { margin-top: 0.75em; }
    th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
    td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
</style>
</head>
<body>
<h1>Open Beta Registration</h1>
<p>Choose a username and password, then click <strong>Submit</strong> to register.</p>
<form method="post" action="index.php" enctype="multipart/form-data" >
      Username  <input type="text" name="username" id="username"/></br>
      Password <input type="text" name="password" id="password"/></br>
      <input type="submit" name="submit" value="Submit" />
</form>
<?php error_reporting(-1); ?>
<?php ini_set('display_errors', true); ?>
<?php
    // Retrieve Database Information	
	define('__ROOT__', dirname(dirname(__FILE__)));
	require_once __ROOT__.'/config.php';
	echo 'config.php accessed'."</br>";
    try { //Establish Database Connection
		$connection = new PDO('mysql:host='.$dbHost.';dbname='.$db, $dbUsername, $dbPassword);
		$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		echo 'Connection Established!'."</br>";
    }
    catch(Exception $e){
		echo 'FAILURE TO CONNECT TO DATABASE'."</br>";
        die(var_dump($e));
    }
	
    if(!empty($_POST)) { //Evaluate Registration Information
		try {
			$username = $_POST['username'];
			$password = $_POST['password'];
			if(trim($username)!='' && trim($password)!='') { // Insert Data
				$sqlInsert = "INSERT INTO users (username, password) VALUES ('$username','$password')";
				$stmt = $connection->prepare($sqlInsert);
				$stmt->bindValue(1, $username);
				$stmt->bindValue(2, $password);
				$stmt->execute();
				echo "<h3>Registration Successful!</h3>";
			} else {
				echo "Invalid Input";
			}
		} catch(Exception $e) {
			die(var_dump($e));
		}
    }
    // Retrieve data
	echo 'Attempting Database Information Retrieval';
    $sqlSelect = "SELECT * FROM users";
    $stmt = $connection->query($sqlSelect);
    $users = $stmt->fetchAll();
    if(count($users) > 0) {
        echo "<h2>Registered users:</h2>";
        echo "<table>";
        echo "<tr><th>ID</th>";
		echo "<th>Username</th>";
        echo "<th>Password</th></tr>";
        foreach($users as $user) {
			echo "<tr><td>".$user['userID']."</td>";
            echo "<td>".$user['username']."</td>";
            echo "<td>".$user['password']."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>No registered users exist.</h3>";
    }
?>
</body>
</html>