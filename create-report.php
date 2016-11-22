<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php

// If we make it this far we must be a logged in user but ensure customer role does not proceed
  $user->redirect_customer();  

  // if form has been submitted
  if (!empty($_POST)){

    // If the submit button was pressed then redirect to output.php for processing
    if(isset($_POST['submit']) || isset($_POST['save'])){     
         require(ROOT_PATH . "output.php");
    }

  }// end if $_POSt submit

?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>
<?php require(ROOT_PATH . "inc/report-nav.php"); ?>

<!-- app main column -->
<div class="app-col-main grid_10 alpha">    

  <?php 

      // Somewhere along the way I forgot to replace this with
      // the Survey Class!!
      // -- need to refactor this code in future!

      create_corrCheck(); 

  ?>   

</div><!-- app-col-main -->

<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>