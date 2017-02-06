<?php

$options = unserialize(DB_OPTIONS);

try {
	$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, $options);	

	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$db->exec("SET NAMES 'utf8'");
	// echo "DB Conn success!";
} catch (Exception $e) {
	echo "Could not connect to the database.";
	exit;
}
