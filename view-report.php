<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/all-includes.php"); ?>
<?php    

      // If no $_GET vars are passed
  if (empty($_GET)) {

  		// redirect  	
    	header("Location: report-management.php");     	
    	die("Redirecting to report-management.php");  // critical!
  }

// Get ID of survey
$survey_id = $_GET ["survey_id"];

// Customer Checks
// 1st we need to check to see if the current user is a customer. If so we must then ensure that the survey id being passed 
// belongs to the company ID of the customer
// If it does then we will allow the page to load else we will redirect to report-listing.php
if($user->user_role == "Customer"){	

	$survey_check = survey_check($survey_id, $user->company_id, $db);
	echo $survey_check;

	if(!survey_check($survey_id, $user->company_id, $db)){		

		header("Location: report-management.php");     	
  		  	die("Redirecting to report-management.php");  // critical!

	}
} // end if user = customer


// Get main survey details
$survey_dets = get_survey_main_dets($survey_id, $db);

?>
<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/header-modified.php"); ?>

	

<!-- app main column -->
<div class="app-col-main grid_12">	

<div class="section-header">
	<div class="btn-ctrl">
		<a href="<?php echo BASE_URL; ?>edit-report.php?surveyid=<?php echo $survey_dets["survey_ID"] ?>" class="btn btn-primary">Edit This Report >></a>
	</div>
</div>

	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Inspection Details</h3>
	  </div>
	  <div class="panel-body">  

	  	<table class="survey-results">
	  		<tr><td>Survey ID:</strong></td><td> <?php echo $survey_dets["survey_ID"]; ?></td></tr>
	  		<tr><td>Vehicle Type:</strong></td><td> <?php echo $survey_dets["vehicle_type"]; ?></td></tr>
	  		<tr><td>Vehicle Reg:</strong></td><td> <?php echo $survey_dets["vehicle_reg"]; ?></td></tr>
	  		<tr><td>Make Model:</strong></td><td> <?php echo $survey_dets["make_model"]; ?></td></tr>
	  		<tr><td>Odometer Reading:</strong></td><td> <?php echo $survey_dets["odo_reading"]; ?> <?php echo $survey_dets["odo_type"]; ?></td></tr>
	  		<tr><td>Pre-service Remarks:</strong></td><td> <?php echo $survey_dets["pre_service_remarks"]; ?></td></tr>
	  		<tr><td>Notes / Parts List:</strong></td><td> <?php echo nl2br($survey_dets["notes_parts_list"]); ?></td></tr>
	  		<tr><td>Survey Date:</strong></td><td> <?php echo $survey_dets["survey_date"]; ?></td></tr>
	  		<tr><td>Company Name:</strong></td><td> <?php echo $survey_dets["company_name"]; ?></td></tr>
	  		<tr><td>Completed By:</strong></td><td> <?php echo $survey_dets["username"]; ?></td></tr>
	  		<tr><td>Survey Status:</strong></td><td> <?php echo $survey_dets["survey_status"]; ?></td></tr>
	  	</table>



	  	

	    
	  </div>
	</div>

	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title">Fault Details</h3>
	  </div>
	  <div class="panel-body">  	  	

	  	<?php report_details($survey_id, $db); ?>

	  </div>

</div>


<?php include($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php"); ?>