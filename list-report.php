 <?php

  require_once("inc/config.php");
  include(ROOT_PATH . "inc/functions.php");

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
  
  <body> 

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

          <h2>Survey List</h2>

          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Inspection No</th>
                <th>Date</th>
                <th>Company</th>
                <th>Inspection By</th>
                <th>Completed By</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <a href="report.php?id=58">58</a>
                </td>
                <td>20/1112014</td>
                <td>Website NI</td>
                <td>G. Watson</td>
                <td>M. Corr</td>
                <td>Darft</td>
              </tr>
            </tbody>
          </table>



            

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