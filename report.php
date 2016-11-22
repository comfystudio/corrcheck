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

    /* ==================== END CONNECTION CODE ==================== */

  $rep_ID = "";
  if (isset($_GET["id"])) {
    $rep_ID = trim($_GET["id"]);
    if ($rep_ID != "") {      
      echo "We will show results for: $rep_ID";
    }
  }

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
  
  <body class="corr-report"> 

    <div class="container">

      <!-- header -->
      <div class="header row">

        <!-- app title -->
        <div class="app-title col-md-10" >
          <h1>Corr Bros Vehicle Inspection</h1>      
        </div>

        <div class="app-acc-dets col-md-2">
          <ul>
            <li>Username here</li>
            <li>Log Out</li>
          </ul>
        </div>

      </div><!-- header -->

      <!-- main content area -->
      <div class="app-main-content row">

        
        <!-- app-sidebar -->
        <div class="app-sidebar col-md-2">
          <p>
            Main Menu Will Go Here
          </p>
        </div><!-- app-sidebar -->

        
        <!-- app main column -->
        <div class="app-col-main col-md-10">

          <div class="row">
            <div class="col-md-12" id="section-nav">

              <a href="#" class="btn btn-primary btn-section-nav" id="section-1" data="veh_dets">Vehicle Details</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-2" data="bk_perf">Brake Performance</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-3" data="section_tyre_thread">Tyre Thread</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-4" data="section_lub">Lubrication</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-5" data="section_lights">Lights</a>
              <a href="#" class="btn btn-primary btn-section-nav not_trailers" id="section-6" data="section_tacho">Tachograph</a>
              <a href="#" class="btn btn-primary btn-section-nav not_trailers" id="section-7" data="section_insdecab">Inside Cab</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-8" data="section_groundlevel">Ground Level</a>
              <a href="#" class="btn btn-primary btn-section-nav not_trailers" id="section-9" data="section_smallservice">Small Service</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-10" data="section_additional">Additional</a>
              <a href="#" class="btn btn-primary btn-section-nav" id="section-11" data="section_rep_details">Report Details</a>              
              
            </div>
          </div>

          <hr>

          <?php echo create_corrCheck(); ?>   

        </div><!-- app-col-main -->


      </div><!-- app-content / row-->      

    </div><!-- container -->

  
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
    <script src="js/bootstrap.min.js"></script>    
    <script src="dist/jquery.validate.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/corrcheck.js"></script>    
    <script type="text/javascript">
    $(document).ready(function() {

      

    });
    </script>

  </body>
</html>