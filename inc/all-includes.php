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
    require_once(ROOT_PATH . "inc/class_the_user.php");
    require_once(ROOT_PATH . "inc/class_config_user.php");
    require_once(ROOT_PATH . "inc/class_user_form.php");
    require_once(ROOT_PATH . "inc/class_config_co.php");
    require_once(ROOT_PATH . "inc/class_company_form.php");
    require_once(ROOT_PATH . "inc/class_survey.php");

    // At the top of the page we check to see whether the user is logged in or not 
    if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: login.php"); 
         
        // Remember that this die statement is absolutely critical.  Without it, 
        // people can view your members-only content without logging in. 
        die("Redirecting to login.php"); 
    } 

    $user = new The_User($_SESSION['user'], $db);  
    $filename = basename($_SERVER['PHP_SELF']);    

    /* ==================== END CONNECTION CODE ==================== */