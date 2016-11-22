<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header.php"); ?>
<?php

  // if form has been submitted
  if (!empty($_POST)){

    // If the submit button was pressed then redirect to output.php for processing
    if(isset($_POST['submit']) || isset($_POST['save'])){     
         require(ROOT_PATH . "output.php");
    }

  }// end if $_POSt submit

?>

<!-- app main column -->
<div class="app-col-main col-xs-10">

  <?php require(ROOT_PATH . "inc/report-nav.php"); ?>

  <hr>

  <?php create_corrCheck(); ?>   

</div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>