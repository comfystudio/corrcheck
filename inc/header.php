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

    /* ==================== END CONNECTION CODE ==================== */

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

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Corr Check Vehicle Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/styles.css" rel="stylesheet" media="screen">    
    <link href="css/datepicker.css" rel="stylesheet" media="screen">   
  </head>
  <body <?php set_css_classes($filename); ?>> 


    <!-- Top Bar -->
      <div class="topbar">
        <div class="topbar-content container_12">                    

            <div class="app-acc-dets">
              <span>Welcome back, <?php echo $user->username;  ?></span> | 
              <a href="<?php echo BASE_URL; ?>logout.php">Log Out</a>
            </div>

        </div><!-- Topbar-content -->
      </div><!-- Topbar -->

    <!-- header -->
    <header class="app-header">
      <div class="app-header-content container_12">

        <div class="toplogo grid_2">
          <img src="http://www.corrbrothers.co.uk/wp-content/themes/corrbrothers/images/corr-brothers-logo.png" title="Tachographs &amp; Commercial Vehicle Services Northern Ireland">          
        </div>

        <?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/top-nav.php"); ?>

      </div>
    </header>

  <!-- header stuff here -->
  <div class="app-primary greytachbk cf"> 
    <div class="primary-content container_12 cf">