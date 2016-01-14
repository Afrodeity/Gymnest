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
      Name  <input type="text" name="username" id="username"/></br>
      Email <input type="text" name="password" id="password"/></br>
      <input type="submit" name="submit" value="Submit" />
</form>
<?php
    // DB connection info
    require_once 'config.php';
    //using the values you retrieved earlier from the Azure Portal.
    // Connect to database.
    try {
		$oConnection = new PDO('mysql:host='.$sHost.'dbname='.$sDb, $sUsername, $sPassword);
        $oConnection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }
    catch(Exception $e){
        die(var_dump($e));
    }
    // Insert registration info
    if(!empty($_POST)) {
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];
        // Insert data
        $sql_insert = "INSERT INTO user (username, password)
                   VALUES (?,?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bindValue(1, $username);
        $stmt->bindValue(2, $password);
        $stmt->execute();
    }
    catch(Exception $e) {
        die(var_dump($e));
    }
    echo "<h3>Registration complete!</h3>";
    }
    // Retrieve data
    $sql_select = "SELECT * FROM user";
    $stmt = $conn->query($sql_select);
    $users = $stmt->fetchAll();
    if(count($users) > 0) {
        echo "<h2>People who are registered:</h2>";
        echo "<table>";
        echo "<tr><th>Username</th>";
        echo "<th>Password</th></tr>";
        foreach($users as $user) {
            echo "<tr><td>".$user['username']."</td>";
            echo "<td>".$user['password']."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>No registered users exist.</h3>";
    }
?>
</body>
</html>