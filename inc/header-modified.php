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