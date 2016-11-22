<?php 

    // Allow user
    // Source: http://forums.devshed.com/php-faqs-stickies-167/program-basic-secure-login-system-using-php-mysql-891201.html

    /* ==================== START CONNECTION CODE ==================== */

    // Get the db connection details
    require_once($_SERVER["DOCUMENT_ROOT"] . "/inc/config.php");

    // Get the Try/Catch block to create the db object
    require_once(ROOT_PATH . "/inc/setup_page_session.php");

    /* ==================== END CONNECTION CODE ==================== */
     
    // We remove the user's data from the session 
    unset($_SESSION['user']); 
     
    // We redirect them to the login page 
    header("Location: login.php"); 
    die("Redirecting to: login.php");