<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php 

// If we make it this far we must be a logged in user but ensure customer role does not proceed
  $user->redirect_customer();  

  // Very first thing to do... do we have a _POST array?
  if (!empty($_POST)){

    // Has the submit button been pressed?
    if(isset($_POST['submit']) || isset($_POST['save']) || isset($_POST['final'])){ 

        // If so then load output.php as we are done here
         require(ROOT_PATH . "output-edit.php");
    }

  }// end if $_POSt submit
  
  // If 'submit' has not been pressed then check for a survey id query strin
  if(isset($_GET['surveyid'])){

    //If found then we are dealing with a fresh load of the edit page - go get details for survey id
    $survey_args = array(
        "surveyID" => $_GET['surveyid'],
        "surveyStatus" => "load"
      );

  }else{

    //We are dealing with a reload of the page and are working with the _POST array
    $survey_args = array(
        "surveyID" => $_POST['surveyid'],
        "surveyStatus" => "reload",
        "posted" => $_POST
      );
  }

  $this_survey = new Survey($db, $survey_args);

  // Check for success message in query string
  $success_check = "";
  $success_message = "";

  if (!empty($_GET)) {
    if (array_key_exists("message",$_GET)){
      if($_GET["message"]!=""){
        $success_check = $_GET["message"];
      }
    }
  }

  if($success_check=="success"){
    $success_message = "Vehicle report successfully saved";
  }
  if($success_check=="final"){
    $success_message = "Vehicle report successfully marked as final!";
  }

// php code ends ?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>


  <?php if($success_message!=""): ?>
    <div class="alert alert-success" role="alert"><strong><?php echo $success_message; ?></strong></div>
  <?php endif; ?>

  <?php require(ROOT_PATH . "inc/report-nav.php"); ?>

<!-- app main column -->
<div class="app-col-main grid_10 alpha">   

  <div class="rep-top-summary">   
    <ul>
      <li><strong>Report Status:</strong> <span class="report-curr-status"><?php echo $this_survey->surveyStatus;  ?></a></li>
      <li><strong>Inspection By:</strong> <?php echo $this_survey->completedByFirstName . " " . $this_survey->completedByLastName ?></li>
    </ul>
  </div><!-- rep-top-summary -->
    

    <?php  // create_corrCheck(); ?>
    <?php $this_survey->buildSurveyForm(); ?>     

</div><!-- app-col-main -->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>