<?php 

  	/* ==================== START CONNECTION CODE ==================== */

  	// Get the db connection details
	require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/config.php");

	// Get the Try/Catch block to create the db object
	require_once(ROOT_PATH . "inc/conn.php");	

	// Create session and setup header
	require_once(ROOT_PATH . "inc/setup_page_session.php");

	// Get the function files
	require_once(ROOT_PATH . "inc/functions.php");

	require_once(ROOT_PATH . "inc/user_class.php");

	/* ==================== END CONNECTION CODE ==================== */	

	$user->userGreating();


?>