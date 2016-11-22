<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header.php"); ?>
<?php

	// Note: We get to this very point either one of three ways:
	// 1. A new report was in the process of being created (create-report.php) and the user clicked save.
	// 2. The user clicked to edit a report from another screen
	// 3. The user clicked on save while editing a report (edit-report.php) and wishes to continue to edit
	
	// In 1 & 2 we will have a _GET array with a survey to deal with a $surveyid
	// - and in this instance the page has been loaded for the first time
	// In 3 we will have a _POST array to work with
	// - and in this instance we are relaoding the page
	
	// Check for a passed surveyid
	if(isset($_GET['surveyid'])){

		echo "We are dealing with a fresh load of the edit page - go get details for survey id: " . $_GET['surveyid'];

	}else{

		echo "we are dealing with a reload of the page and are working with a _POST array";
	}

	$this_survey = new Survey($db);




	





?>

<!-- app main column -->
<div class="app-col-main col-md-10">

	<?php require(ROOT_PATH . "inc/report-nav.php"); ?>

  	<hr>

  	<?php  // create_corrCheck(); ?>

  	<?php //$this_survey->buildSurveyForm(); ?>   	

</div><!-- app-col-main -->


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>