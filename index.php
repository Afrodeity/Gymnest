<?php
require_once 'config.php';
try {
	$oConnection = new PDO('mysql:host='.$sHost.'dbname='.$sDb, $sUsername, $sPassword);
	$oConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$oStatement = $oConnection->prepare('SELECT userID FROM `user`');
	$oResult = $oStatement->fetchAll();
	
	foreach ($oResult as $aRow) {
		print_r($aRow['userID']);
	}
} catch(PDOException $e) {
	echo 'ERROR: ' . $e->getMessage();
}
echo 'Hello World';
>