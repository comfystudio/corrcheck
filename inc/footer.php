

        <?php if (!stripos($_SERVER['REQUEST_URI'], 'vehicle-dashboard.php')){?>
            </div><!-- app-primary-content -->
                </div><!-- app-primary -->
        <?php } ?>



            <!-- footerr -->
        <div class="site-footer hidden-print">

            <div class="container_12">

                <div class="grid_6 alpha">
                    &copy; <?php date('Y'); ?> Corr Brothers Ltd
                </div>

                <div class="designedby grid_6 omega">
                    Designed &amp; Developed by <a href="http://websiteni.com">Website NI</a>
                </div>            

            </div><!-- grid_12 -->

        </div><!-- footer -->
  
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
<!--    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>-->
    <script src="js/bootstrap.min.js"></script>
    <script src="dist/jquery.validate.js"></script>
    <?php if (!stripos($_SERVER['REQUEST_URI'], 'vehicle-dashboard.php')){?>
        <script src="js/jquery-ui.min.js"></script>
    <?php } ?>
    <script src="js/bootstrap-datepicker.js"></script>

    <script src="js/corrcheck.js"></script>

    <?php if (isset($cameraScript) && $cameraScript == true){?>
        <script src="js/modernizr.min.js"></script>
        <script src="js/jquery.Jcrop.js"></script>
        <script src="js/ocrad.min.js"></script>
        <script src="js/glfx.min.js"></script>
        <script src="js/jcrop-main.js"></script>
    <?php } ?>
    <script type="text/javascript">
    $(document).ready(function() {
//        $('.form-wrap .datepicker').datepicker();
        // The code below should only fire on create-report.php
        // And edit-report.php
        
        // var page = location.pathname.substring(1);
        // var allowleave = false;        
        
        // if((page == "edit-report.php") || (page == "create-report.php"))
        // {
        //     $("form.corrCheck_form button.btn").click(function(){
        //         console.log("clicked");
        //         var allowleave = true;        
        //         console.log(allowleave);
        //     });

        //     window.onbeforeunload = function() {
        //         if(allowleave == false)
        //         return "You're about to end your session, are you sure?";
        //     }
        // }
    });      
    </script>
  </body>
</html>